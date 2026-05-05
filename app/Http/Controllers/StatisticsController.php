<?php
namespace App\Http\Controllers;

use App\Services\StatisticService;

class StatisticsController extends Controller
{
    public function index(StatisticService $statisticService)
    {
        $dailyStats = $statisticService->getDailyStats();

        return view('statistics.index', compact('dailyStats'));
    }
}
