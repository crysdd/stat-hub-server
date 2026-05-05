<?php
namespace App\Services;

use App\Data\HitData;
use App\Models\Statistic;
use Illuminate\Http\Request;
use UAParser\Parser;

class StatisticService
{
    public function store(Request $request)
    {
        $requestData = $request->all();
        $parser      = Parser::create();
        $userAgent   = data_get($requestData, 'header.user-agent.0') ?? data_get($requestData, 'header.user-agent');
        $language    = data_get($requestData, 'header.accept-language.0') ?? data_get($requestData, 'header.accept-language');

        // Parse user agent on server side
        $result = $parser->parse($userAgent);

        // Extract and decode query parameters
        $referrer         = urldecode(data_get($requestData, 'data.r'));
        $screenResolution = data_get($requestData, 'data.s');
        $browserUrl       = urldecode(data_get($requestData, 'data.u'));

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
            'language'          => $language,
            'page_url'          => $browserUrl ?: null,
            'client_ip'         => data_get($requestData, 'client_ip'),
            'referrer'          => $referrer,
        ]);

        // Store the statistics
        Statistic::create($hitData->toArray());
    }

    public function getDailyStats()
    {
        return Statistic::selectRaw('DATE(created_at) as date, COUNT(DISTINCT client_ip) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();
    }

    // public function getDailyHitStats()
    // {
    //     return Statistic::selectRaw('DATE(created_at) as date, COUNT(*) as count')
    //         ->groupBy('date')
    //         ->orderBy('date', 'desc')
    //         ->get();
    // }

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

}
