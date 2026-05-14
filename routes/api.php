<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\DeviceHeartbeatController;
use App\Http\Controllers\Api\WaterLevelHistoryController;
use App\Http\Controllers\Api\NotificationApiController;

Route::get('/devices/heartbeat', [DeviceHeartbeatController::class, 'check']);
Route::get('/sensor-data/latest/{slug?}', [SensorDataController::class, 'latest']);
Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::post('/device/status', [SensorDataController::class, 'updateStatus']);
Route::post('/device/update-config', [SensorDataController::class, 'updateConfig']);
Route::get('/water-level/history', [WaterLevelHistoryController::class, 'index']);
Route::get('/notifications/active-alert', [NotificationApiController::class, 'getActiveAlert']);
