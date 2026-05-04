<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;

Route::get('/sensor-data/latest/{slug?}', [SensorDataController::class, 'latest']);
Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::post('/device/status', [SensorDataController::class, 'updateStatus']);
Route::post('/device/update-config', [SensorDataController::class, 'updateConfig']);
