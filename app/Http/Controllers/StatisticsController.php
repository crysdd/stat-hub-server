<?php
namespace App\Http\Controllers;

use App\Services\StatisticService;
use Illuminate\Support\Carbon;

class StatisticsController extends Controller
{
    public function index(StatisticService $statisticService)
    {
        $month = request('month');
        $host = request('host');
        // Determine the month to fetch statistics for
        if ($month) {
            // Validate the month parameter (format: YYYY-MM)
            try {
                $currentDate = Carbon::parse($month . '-01');

                // Prevent future months beyond current date
                if ($currentDate->isFuture() || $currentDate->isCurrentMonth() && !$host) {
                    return redirect()->route('statistics.index');
                }
            } catch (\Exception $e) {
                // If invalid month parameter, use current month
                $currentDate = Carbon::now()->startOfMonth();
            }
        } else {
            // Default to current month
            $currentDate = Carbon::now()->startOfMonth();
        }

        // Get previous month, but only if there are records
        $earliestMonth = $statisticService->getEarliestMonth();

        if ($currentDate < $earliestMonth) {
            // don't show empty graphs from the past
            abort(404);
        }
        if ($currentDate->copy()->subMonth() < $earliestMonth) {
            // If the previous month has no records, we can't navigate further back
            $previousMonth = null;
        } else {
            $previousMonth = $currentDate->copy()->subMonth()->format('Y-m-d');
        }

        // Get next month (only if not current month)
        $nextMonth = $currentDate->isCurrentMonth()
            ? null
            : $currentDate->copy()->addMonth()->format('Y-m-d');

        // Get current month's statistics
        $dailyStats = $statisticService->getDailyStatsForMonth($currentDate);
        $hosts = $statisticService->getCountByHostForMonth($currentDate);

        // Get current month's statistics
        $dailyStats = $statisticService->getDailyStatsForMonth($currentDate, $host);
        $hosts = $statisticService->getCountByHostForMonth($currentDate);

        // Add pagination info to the collection for view
        $dailyStats->previous_month = $previousMonth;
        $dailyStats->next_month = $nextMonth;
        $dailyStats->current_month = $currentDate->format('Y-m-d');
        $dailyStats->host = $host;

        return view('statistics.index', compact('dailyStats', 'hosts'));
    }
}

