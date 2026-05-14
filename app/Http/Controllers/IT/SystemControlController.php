<?php

namespace App\Http\Controllers\IT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemControlController extends Controller
{
    public function toggleLockdown(Request $request)
    {
        $current = DB::table('system_settings')->where('key', 'system_lockdown')->value('value');
        $newStatus = ($current === '1') ? '0' : '1';

        DB::table('system_settings')
            ->where('key', 'system_lockdown')
            ->update(['value' => $newStatus, 'updated_at' => now()]);

        return response()->json([
            'status' => 'success',
            'lockdown' => $newStatus,
            'message' => ($newStatus === '1') ? 'SYSTEM_EMERGENCY_LOCKDOWN_ACTIVATED' : 'SYSTEM_ACCESS_RESTORED'
        ]);
    }

    public function getStatus()
    {
        $lockdown = DB::table('system_settings')->where('key', 'system_lockdown')->value('value');
        return response()->json(['lockdown' => $lockdown]);
    }

    public function getActivityLogs()
    {
        $logs = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->name : 'Unknown/Guest',
                    'event' => $log->event_type,
                    'desc' => $log->description,
                    'time' => $log->created_at->diffForHumans(),
                    'ip' => $log->ip_address
                ];
            });
            
        return response()->json($logs);
    }
}
