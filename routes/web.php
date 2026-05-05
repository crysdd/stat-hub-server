<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImgController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/img', ImgController::class);
Route::any('/hit', HitController::class);

Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
