<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorDataController;

Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);
Route::post('/sensor-data', [SensorDataController::class, 'store']);
Route::post('/device/status', [SensorDataController::class, 'updateStatus']);
