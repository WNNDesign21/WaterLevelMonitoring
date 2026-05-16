<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\DeviceHeartbeatController;
use App\Http\Controllers\Api\WaterLevelHistoryController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\AuthController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
});

Route::get('/devices', [SensorDataController::class, 'list']);
Route::get('/devices/heartbeat', [DeviceHeartbeatController::class, 'check']);
Route::get('/sensor-data/latest/{slug?}', [SensorDataController::class, 'latest']);
Route::get('/sensor-data/stats/{slug}', [SensorDataController::class, 'stats']);
Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::post('/device/status', [SensorDataController::class, 'updateStatus']);
Route::post('/device/update-config', [SensorDataController::class, 'updateConfig']);
Route::get('/water-level/history', [WaterLevelHistoryController::class, 'index']);
Route::get('/water-level/export', [WaterLevelHistoryController::class, 'export']);
Route::get('/notifications/active-alert', [NotificationApiController::class, 'getActiveAlert']);
Route::get('/weather', [WeatherController::class, 'current']);
