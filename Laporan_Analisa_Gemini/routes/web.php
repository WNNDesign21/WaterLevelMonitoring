<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DeviceController;

Route::get('/', function () {
    $primaryDevice = \App\Models\Device::where('slug', 'cybernova-s400-primary')->first();
    return view('dashboard', compact('primaryDevice'));
})->name('dashboard');

Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
Route::get('/devices/{slug}', [DeviceController::class, 'show'])->name('devices.show');
Route::post('/devices/{slug}/location', [DeviceController::class, 'updateLocation'])->name('devices.update_location');
