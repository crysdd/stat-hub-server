<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImgController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Statistics API routes
Route::get('/test', ImgController::class);
Route::get('/hit', HitController::class);
// Route::get('/api/statistics', [StatisticsController::class, 'getStats']);

// Statistics page
// Route::get('/statistics', function () {
//     return view('statistics');
// })->name('statistics');

