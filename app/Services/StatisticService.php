<?php
namespace App\Services;

use App\Jobs\HitJob;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use UAParser\Parser;

class StatisticService
{
    public function store(Request $request)
    {
        $requestData = $request->all();

        HitJob::dispatch($requestData);
    }

    public function getDailyStatsForMonth(Carbon $month, ?string $host = null)
    {
        // Get start and end of the specified month
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();

        // Get statistics for the month, grouped by date
        $statsData = Statistic::selectRaw("
            DATE(created_at) as date,
            COUNT(*) as count_hit,
            COUNT(DISTINCT CONCAT_WS('||', browser_name, browser_version, os_name, screen_resolution, color_depth, language, client_ip)) as count
        ")
            ->where('created_at', '>=', $startOfMonth)
            ->where('created_at', '<=', $endOfMonth)
            ->when($host, function ($query) use ($host) {
                $query->where('host', $host);
            })
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        // Create a date range for all days in the month
        $currentDate = $startOfMonth->copy();
        $endDate     = $endOfMonth->copy();
        $dateRange   = collect();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            $dateRange->put($dateString, (object) [
                'date'      => $dateString,
                'count'     => $statsData[$dateString]->count ?? 0,
                'count_hit' => $statsData[$dateString]->count_hit ?? 0,
            ]);

            $currentDate->addDay();
        }

        return collect($dateRange->values()->all());
    }

    public function getEarliestMonth(): Carbon
    {
        $earliestRecord = Statistic::min('created_at');

        return $earliestRecord
            ? Carbon::parse($earliestRecord)->startOfMonth()
            : Carbon::now()->startOfMonth();
    }

    // Count unique users by browser
    public function getCountByBrowser()
    {
        return Statistic::selectRaw('browser_name, COUNT(DISTINCT client_ip) as count')
            ->groupBy('browser_name')
            ->orderBy('count', 'desc')
            ->get();
    }

    // Count unique users by OS
    public function getCountByOs()
    {
        return Statistic::selectRaw('os_name, COUNT(DISTINCT client_ip) as count')
            ->groupBy('os_name')
            ->orderBy('count', 'desc')
            ->get();
    }

    // Count unique users by host for a specific month
    public function getCountByHostForMonth(Carbon $month)
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();

        return Statistic::query()
            ->selectRaw('host, COUNT(*) as count')
            ->where('created_at', '>=', $startOfMonth)
            ->where('created_at', '<=', $endOfMonth)
            ->groupBy('host')
            ->orderByDesc('count')
            ->get();
    }
}

