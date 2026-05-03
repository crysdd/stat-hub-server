<?php

declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Services\StatisticService;
use Illuminate\Http\Request;

class HitController extends Controller
{
    public function __invoke(Request $request, StatisticService $service)
    {
        $service->store($request);

        $response = response()->make("", 200);
        $response->header('Content-Type', 'image/png');

        return $response;
    }
}
