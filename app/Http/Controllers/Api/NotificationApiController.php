<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\NotificationLog;

class NotificationApiController extends Controller
{
    /**
     * Get active alert status for mobile app
     */
    public function getActiveAlert(Request $request)
    {
        $request->validate([
            'device_slug' => 'nullable|string|exists:devices,slug'
        ]);

        $query = Device::query();
        if ($request->device_slug) {
            $query->where('slug', $request->device_slug);
        }

        $devices = $query->get();
        $alerts = [];

        foreach ($devices as $device) {
            $lastLog = NotificationLog::where('device_id', $device->id)
                ->orderBy('sent_at', 'desc')
                ->first();

            $status = $lastLog ? $lastLog->status_level : 'Normal';
            
            $alerts[] = [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'device_slug' => $device->slug,
                'current_status' => $status,
                'is_emergency' => in_array($status, ['Siaga 1', 'Siaga 2']),
                'must_evacuate' => ($status === 'Siaga 1'),
                'last_alert_at' => $lastLog ? $lastLog->sent_at->toIso8601String() : null,
                'alert_tone' => $this->getAlertTone($status),
            ];
        }

        return response()->json([
            'status' => 'success',
            'alerts' => $alerts
        ]);
    }

    private function getAlertTone($status)
    {
        switch ($status) {
            case 'Siaga 1': return 'emergency_siren_heavy';
            case 'Siaga 2': return 'warning_beep_fast';
            case 'Siaga 3': return 'notification_simple';
            default: return 'none';
        }
    }
}
