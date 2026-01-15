<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'sendResetEmail']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Dashboard API
Route::middleware('auth:api')->prefix('dashboard')->group(function () {
    Route::get('stats', [DashboardController::class, 'stats']);
    Route::get('daily-report', [DashboardController::class, 'dailyReport']);
    Route::get('weekly-report', [DashboardController::class, 'weeklyReport']);
    Route::get('monthly-report', [DashboardController::class, 'monthlyReport']);
    Route::get('sleep-time-chart', [DashboardController::class, 'sleepTimeChart']);
});

// Jurnal API
Route::middleware('auth:api')->prefix('jurnal')->group(function () {
    Route::get('daily', [JurnalController::class, 'daily']);
    Route::get('weekly', [JurnalController::class, 'weekly']);
    Route::get('monthly', [JurnalController::class, 'monthly']);
});

// Report API
Route::middleware('auth:api')->prefix('report')->group(function () {
    Route::get('daily', [ReportController::class, 'daily']);
    Route::get('weekly', [ReportController::class, 'weekly']);
    Route::get('monthly', [ReportController::class, 'monthly']);
});

// Users API
Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('stats', [UserController::class, 'stats']);
    Route::get('/', [UserController::class, 'index']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::post('/{id}/reset-password', [UserController::class, 'resetPassword']);
});
