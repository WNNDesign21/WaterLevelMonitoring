<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use App\Events\DeviceStatusUpdated;

class DeviceHeartbeatController extends Controller
{
    public function check()
    {
        $threshold = now()->subSeconds(35); // Sedikit lebih longgar dari 30s

        $devices = Device::all();
        $results = [];

        foreach ($devices as $device) {
            $isIdle = $device->last_seen && $device->last_seen < $threshold;
            
            // Jika idle dan masih tercatat online, update ke offline
            if ($isIdle && $device->status === 'online') {
                $device->update(['status' => 'offline']);
                broadcast(new DeviceStatusUpdated($device));
            } 
            // Jika tidak idle tapi tercatat offline, update ke online
            else if (!$isIdle && $device->status === 'offline' && $device->last_seen > $threshold) {
                $device->update(['status' => 'online']);
                broadcast(new DeviceStatusUpdated($device));
            }

            $results[] = [
                'slug' => $device->slug,
                'name' => $device->name,
                'status' => $device->status,
                'last_seen_human' => $device->last_seen ? $device->last_seen->diffForHumans() : 'Never',
                'is_online' => $device->status === 'online'
            ];
        }

        return response()->json([
            'status' => 'success',
            'timestamp' => now()->toISOString(),
            'devices' => $results
        ]);
    }
}
