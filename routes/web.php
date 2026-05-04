<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DeviceController;

Route::get('/', function () {
    $primaryDevice = \App\Models\Device::where('slug', 'node-wifi-wemos-d1-69f01e5649f84')->first();
    $allDevices = \App\Models\Device::where('status', '!=', 'maintenance')->get();
    return view('user_dashboard', compact('primaryDevice', 'allDevices'));
})->name('user.dashboard');

Route::get('/dashboard/{slug}', function ($slug) {
    $primaryDevice = \App\Models\Device::where('slug', $slug)->firstOrFail();
    $allDevices = \App\Models\Device::where('status', '!=', 'maintenance')->get();
    return view('user_dashboard', compact('primaryDevice', 'allDevices'));
})->name('user.dashboard.device');

Route::get('/it', function () {
    $primaryDevice = \App\Models\Device::where('slug', 'node-wifi-wemos-d1-69f01e5649f84')->first();
    $allDevices = \App\Models\Device::all();
    return view('it_dashboard', compact('primaryDevice', 'allDevices'));
})->name('it.dashboard');

Route::post('/it/devices', [DeviceController::class, 'store'])->name('devices.store');
Route::put('/it/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
Route::delete('/it/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
Route::get('/devices/{slug}', [DeviceController::class, 'show'])->name('devices.show');
Route::post('/devices/{slug}/location', [DeviceController::class, 'updateLocation'])->name('devices.update_location');
