<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// SSO Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/forgot-password/whatsapp', [AuthController::class, 'sendResetLinkViaWhatsApp'])->name('password.whatsapp');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

// Profile Completion & Password Change
Route::middleware(['auth'])->group(function () {
    Route::get('/register/complete', [AuthController::class, 'showCompleteProfile'])->name('register.complete');
    Route::post('/register/complete', [AuthController::class, 'completeProfile']);
    
    // Force Password Change Routes
    Route::get('/auth/change-password', [AuthController::class, 'showChangePassword'])->name('password.change.show');
    Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('password.change.store');
});

// Public Dashboard (Guest Access) - Under Lockdown Protection
Route::middleware(['system.lockdown'])->group(function() {
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
});

// Protected Routes (IT & Admin) - HIGH SECURITY AREA
Route::middleware(['auth', 'password.change.enforce', 'it.admin'])->prefix('it')->name('it.')->group(function () {
    
    // IT Dashboard (Now Protected by it.admin)
    Route::get('/', function () {
        $primaryDevice = \App\Models\Device::where('slug', 'node-wifi-wemos-d1-69f01e5649f84')->first();
        $allDevices = \App\Models\Device::all();
        return view('it_dashboard', compact('primaryDevice', 'allDevices'));
    })->name('dashboard');

    // User Management
    Route::get('/users', [\App\Http\Controllers\IT\UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/history', [\App\Http\Controllers\IT\UserManagementController::class, 'history'])->name('users.history');
    Route::post('/users', [\App\Http\Controllers\IT\UserManagementController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/role', [\App\Http\Controllers\IT\UserManagementController::class, 'updateRole'])->name('users.update-role');
    Route::delete('/users/{user}', [\App\Http\Controllers\IT\UserManagementController::class, 'destroy'])->name('users.destroy');

    // Analytics Engine
    Route::get('/analytics', [\App\Http\Controllers\IT\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [\App\Http\Controllers\IT\AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/export', [\App\Http\Controllers\IT\AnalyticsController::class, 'exportCsv'])->name('analytics.export');

    // Device Management (Now Protected by it.admin)
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/{slug}', [DeviceController::class, 'show'])->name('devices.show');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::put('/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::post('/devices/{slug}/location', [DeviceController::class, 'updateLocation'])->name('devices.update_location');

    // System Global Control (Kill-Switch)
    Route::post('/system/lockdown', [\App\Http\Controllers\IT\SystemControlController::class, 'toggleLockdown'])->name('system.lockdown.toggle');
    Route::get('/system/status', [\App\Http\Controllers\IT\SystemControlController::class, 'getStatus'])->name('system.status');
    Route::get('/system/logs', [\App\Http\Controllers\IT\SystemControlController::class, 'getActivityLogs'])->name('system.logs');
});
