<?php
namespace App\Jobs;

use App\Data\HitData;
use App\Models\Statistic;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use UAParser\Parser;

class HitJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $requestData,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $parser    = Parser::create();
        $userAgent = data_get($this->requestData, 'header.user-agent.0') ?? data_get($this->requestData, 'header.user-agent');
        $language  = data_get($this->requestData, 'header.accept-language.0') ?? data_get($this->requestData, 'header.accept-language');
        $host      = data_get($this->requestData, 'header.host.0') ?? data_get($this->requestData, 'header.host');

        // Parse user agent on server side
        $result = $parser->parse($userAgent);

        // Extract and decode query parameters
        $referrer         = urldecode(data_get($this->requestData, 'data.r'));
        $screenResolution = data_get($this->requestData, 'data.s');
        $browserUrl       = urldecode(data_get($this->requestData, 'data.u'));

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
            'host'              => $host,
            'page_url'          => $browserUrl ?: null,
            'client_ip'         => data_get($this->requestData, 'client_ip'),
            'referrer'          => $referrer,
        ]);

        // Store the statistics
        Statistic::create($hitData->toArray());
    }
}
