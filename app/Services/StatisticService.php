<?php

namespace App\Services;

use App\Data\HitData;
use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use UAParser\Parser;

class StatisticService
{
    public function store(Request $request)
    {
        $parser    = Parser::create();
        $userAgent = $request->header('User-Agent') ?? '';
        // dd($userAgent);

        // Parse user agent on server side
        $result = $parser->parse($userAgent);

        // Extract and decode query parameters
        $referrer = urldecode($request->input('r', ''));
        $screenResolution = $request->input('s', '');
        $browserUrl       = urldecode($request->input('u', ''));

        // Parse screen resolution from format like "1920*1080*24"
        $screenWidth  = null;
        $screenHeight = null;
        $colorDepth   = 24; // Default fallback

        if ($screenResolution) {
            $parts = explode('*', $screenResolution);
            if (count($parts) >= 2) {
                $screenWidth  = isset($parts[0]) ? intval($parts[0]) : null;
                $screenHeight = isset($parts[1]) ? intval($parts[1]) : null;
                if (count($parts) >= 3) {
                    $colorDepth = intval($parts[2]);
                }
            }
        }

        $hitData = HitData::from([
            'user_agent'        => $userAgent,
            'browser_name'      => $result->ua->family ?? 'Unknown',
            'browser_version'   => ($result->ua->major ?? '') . '.' . ($result->ua->minor ?? ''),
            'os_name'           => $result->os->family ?? 'Unknown',
            'screen_resolution' => $screenWidth && $screenHeight ? $screenWidth . 'x' . $screenHeight : null,
            'color_depth'       => $colorDepth > 0 ? $colorDepth : 24,
            'language'          => $request->header('Accept-Language') ?: 'unknown',
            'page_url'          => $browserUrl ?: null,
            'client_ip'         => $request->getClientIp(),
            'referrer'          => $referrer,
        ]);

        // Store the statistics
        Statistic::create($hitData->toArray());
    }

    /**
     * Get overall statistics
     */
//     public function getOverallStats(): array
//     {
//         $totalVisitors = Statistic::query()
//             ->count('*');
//         $recentVisitors = Statistic::query()
//             ->where('created_at', '>=', now()
//             ->subDay())
//             ->count('*');

//         // Browser distribution
//         $browserStats = Statistic::query()
//             ->select('browser_name', DB::raw('count(*) as count'))
//             ->groupBy('browser_name')
//             ->get();

//         // OS distribution
//         $osStats = Statistic::query()
//             ->select('os_name', DB::raw('count(*) as count'))
//             ->groupBy('os_name')
//             ->get();

//         // Screen resolution distribution
//         $screenStats = Statistic::query()
//             ->select('screen_resolution', DB::raw('count(*) as count'))
//             ->groupBy('screen_resolution')
//             ->orderByDesc('count')
//             ->get();

//         // Referrer/host distribution
//         $referrerStats = Statistic::query()
//             ->select(
//                 DB::raw("COALESCE(NULLIF(referrer, ''), 'Direct') as host"),
//                 DB::raw('count(*) as count')
//             )
//             ->groupBy('host')
//             ->orderByDesc('count')
//             ->get();

//         return [
//             'total_visitors' => $totalVisitors,
//             'recent_visitors' => $recentVisitors,
//             'browser_distribution' => $browserStats,
//             'os_distribution' => $osStats,
//             'screen_resolution_distribution' => $screenStats,
//             'referrer_distribution' => $referrerStats,
//         ];
//     }

//     /**
//      * Get time-based statistics
//      */
//     public function getTimeBasedStats(): array
//     {
//         // Daily visitors (last 7 days)
//         $dailyVisitors = Statistic::query()
//             ->select(
//                 DB::raw("DATE(created_at) as date"),
//                 DB::raw('count(*) as count')
//             )
//             ->where('created_at', '>=', now()->subDays(6))
//             ->groupBy(DB::raw("DATE(created_at)"))
//             ->orderBy('date', 'desc')
//             ->get();

//         // Weekly visitors (last 4 weeks) - PostgreSQL compatible
//         // $weeklyVisitors = Statistic::select(
//         //         DB::raw("EXTRACT(WEEK FROM created_at)::INT as week"),
//         //         DB::raw("TO_CHAR(created_at, 'YYYY') || '-W' || LPAD(EXTRACT(WEEK FROM created_at)::INT::TEXT, 2, '0') as week_label"),
//         //         DB::raw('count(*) as count')
//         //     )
//         //     ->where('created_at', '>=', now()->subWeeks(4))
//         //     ->groupBy(DB::raw("EXTRACT(YEAR FROM created_at), EXTRACT(WEEK FROM created_at)"))
//         //     ->orderBy('week', 'desc')
//         //     ->get();

//         // Monthly visitors (last 12 months) - PostgreSQL compatible
//         // $monthlyVisitors = Statistic::select(
//         //         DB::raw("TO_CHAR(created_at, 'YYYY-MM') as month"),
//         //         DB::raw("TO_CHAR(created_at, 'Mon YYYY') as month_label"),
//         //         DB::raw('count(*) as count')
//         //     )
//         //     ->where('created_at', '>=', now()->subMonths(11))
//         //     ->groupBy(DB::raw("EXTRACT(YEAR FROM created_at), EXTRACT(MONTH FROM created_at)"))
//         //     ->orderBy('month', 'desc')
//         //     ->get();

//         return [
//             'daily_visitors' => $dailyVisitors ?? collect([]),
//             'weekly_visitors' => $weeklyVisitors ?? collect([]),
//             'monthly_visitors' => $monthlyVisitors ?? collect([]),
//         ];
//     }

//     /**
//      * Get breakdown statistics by day (last 7 days)
//      */
//     public function getBreakdownByDay(): array
//     {
//         // OS distribution by day (last 7 days)
//         $osByDay = Statistic::query()
//             ->select(
//                 DB::raw("DATE(created_at) as date"),
//                 'os_name',
//                 DB::raw('count(*) as count')
//             )
//             ->where('created_at', '>=', now()->subDays(6))
//             ->groupBy(DB::raw("DATE(created_at)"), 'os_name')
//             ->orderByDesc('date')
//             ->get();

//         // Browser distribution by day (last 7 days)
//         $browserByDay = Statistic::query()
//             ->select(
//                 DB::raw("DATE(created_at) as date"),
//                 'browser_name',
//                 DB::raw('count(*) as count')
//             )
//             ->where('created_at', '>=', now()->subDays(6))
//             ->groupBy(DB::raw("DATE(created_at)"), 'browser_name')
//             ->orderByDesc('date')
//             ->get();

//         return [
//             'os_by_day' => $osByDay,
//             'browser_by_day' => $browserByDay,
//         ];
//     }

//     /**
//      * Get all statistics in one call
//      */
//     public function getStats(): array
//     {
//         return array_merge(
//             $this->getOverallStats(),
//             $this->getTimeBasedStats(),
//             $this->getBreakdownByDay()
//         );
//     }
}
