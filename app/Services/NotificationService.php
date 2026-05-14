<?php

namespace App\Services;

use App\Models\Device;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Decision logic for sending notifications
     */
    public function checkAndNotify(Device $device, float $tma)
    {
        $currentStatus = $this->getStatusFromTma($device, $tma);
        $lastLog = NotificationLog::where('device_id', $device->id)
            ->orderBy('sent_at', 'desc')
            ->first();

        // 1. If it's Normal and last was Normal, do nothing
        if ($currentStatus === 'Normal' && (!$lastLog || $lastLog->status_level === 'Normal')) {
            return;
        }

        // 2. Logic for Alert Escalation (Worse)
        // If current status is worse than last notified status, NOTIFY IMMEDIATELY
        if ($this->isWorse($currentStatus, $lastLog->status_level ?? 'Normal')) {
            $this->sendNotification($device, $currentStatus, $tma, "IMMEDIATE_ESCALATION");
            return;
        }

        // 3. Logic for Cooldown (Persistent Alert)
        // If status is the same, check cooldown based on severity
        if ($currentStatus !== 'Normal' && $currentStatus === ($lastLog->status_level ?? 'Normal')) {
            $cooldownMinutes = $this->getCooldownMinutes($currentStatus);
            $lastSent = Carbon::parse($lastLog->sent_at);

            if ($lastSent->diffInMinutes(Carbon::now()) >= $cooldownMinutes) {
                $this->sendNotification($device, $currentStatus, $tma, "PERSISTENT_REMINDER");
                return;
            }
        }

        // 4. Logic for Recovery (Getting better)
        // If current is better than last, notify once about the decrease
        if ($this->isBetter($currentStatus, $lastLog->status_level ?? 'Normal')) {
            $this->sendNotification($device, $currentStatus, $tma, "STATUS_RECOVERY");
            return;
        }
    }

    private function getStatusFromTma(Device $device, float $tma)
    {
        if ($tma >= $device->siaga1_threshold) return 'Siaga 1';
        if ($tma >= $device->siaga2_threshold) return 'Siaga 2';
        if ($tma >= $device->siaga3_threshold) return 'Siaga 3';
        return 'Normal';
    }

    private function getCooldownMinutes($status)
    {
        switch ($status) {
            case 'Siaga 1': return 5;   // Very intense (5 mins)
            case 'Siaga 2': return 30;  // Moderate (30 mins)
            case 'Siaga 3': return 120; // Relaxed (2 hours)
            default: return 1440;       // 24 hours for Normal
        }
    }

    private function isWorse($current, $last)
    {
        $levels = ['Normal' => 0, 'Siaga 3' => 1, 'Siaga 2' => 2, 'Siaga 1' => 3];
        return $levels[$current] > $levels[$last];
    }

    private function isBetter($current, $last)
    {
        $levels = ['Normal' => 0, 'Siaga 3' => 1, 'Siaga 2' => 2, 'Siaga 1' => 3];
        return $levels[$current] < $levels[$last];
    }

    private function sendNotification(Device $device, $status, $tma, $reason)
    {
        // 1. Log to database for audit
        NotificationLog::create([
            'device_id' => $device->id,
            'status_level' => $status,
            'tma_value' => $tma,
            'sent_at' => Carbon::now()
        ]);

        // 2. Find Users near this device (Proximity Alert)
        // We find users where THIS device is their closest node, or within 2km radius
        $nearbyUsers = \App\Models\User::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->filter(function($user) use ($device) {
                $distance = $this->calculateDistance($user->latitude, $user->longitude, $device->latitude, $device->longitude);
                return $distance <= 2.5; // Radius 2.5 KM
            });

        foreach ($nearbyUsers as $user) {
            Log::info("DISPATCH_NOTIFICATION: To User {$user->name} ({$user->phone}) for Device {$device->name}. Reason: {$reason}, Status: {$status}");
            
            // Here we would call FCM to push specifically to this user's device
            // Or WhatsApp API if integrated
        }

        Log::info("WATER_SENSE_SUMMARY: [{$reason}] Alert processed for device {$device->name}. Notified " . $nearbyUsers->count() . " users.");
    }

    /**
     * Calculate distance between two points (Haversine formula) in KM
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}
