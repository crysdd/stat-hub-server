<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class HitData extends Data
{
    public function __construct(
        public string $userAgent,
        public string $browserName,
        public string $browserVersion,
        public string $osName,
        public string $screenResolution,
        public string $colorDepth,
        public string $language,
        public string $host,
        public string $pageUrl,
        public string $clientIp,
        public string $referrer,
    ) {}
}
