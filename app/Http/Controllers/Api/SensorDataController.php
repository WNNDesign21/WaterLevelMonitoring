<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Events\SensorDataReceived;

class SensorDataController extends Controller
{
    public function store(Request $request, \App\Services\NotificationService $notificationService)
    {
        $request->validate([
            'device_slug' => 'required|string|exists:devices,slug', // Hardware wajib kirim slugnya
            'distance' => 'required|numeric',
            'valid_count' => 'nullable|integer'
        ]);

        $device = \App\Models\Device::where('slug', $request->device_slug)->first();

        $data = SensorData::create([
            'device_id' => $device->id, // Hubungkan data dengan ID device
            'distance' => $request->distance,
            'valid_count' => $request->valid_count ?? 1,
        ]);

        // Update Status Device yang bersangkutan
        $device->update([
            'status' => 'online',
            'last_seen' => now()
        ]);

        // Process Smart Notifications
        $tma = $device->calculateTma($data->distance);
        $notificationService->checkAndNotify($device, $tma);

        // Broadcast dengan data device
        broadcast(new SensorDataReceived($data, $device));

        return response()->json([
            'status' => 'success',
            'message' => 'Sensor data recorded and broadcasted successfully',
            'data' => $data
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|exists:devices,slug',
            'status' => 'required|string|in:online,offline,maintenance'
        ]);

        $device = \App\Models\Device::where('slug', $request->slug)->first();
        $device->update([
            'status' => $request->status,
            'last_seen' => now()
        ]);

        broadcast(new \App\Events\DeviceStatusUpdated($device))->toOthers();
 
         return response()->json([
             'status' => 'success',
             'message' => "Device {$request->slug} is now {$request->status}"
         ]);
     }
 
     public function updateConfig(Request $request)
     {
         $request->validate([
             'device_slug' => 'required|string|exists:devices,slug',
             'elevation_mdpl' => 'required|numeric',
             'sensor_to_bank' => 'required|integer',
             'river_depth' => 'required|integer',
         ]);
 
         $device = \App\Models\Device::where('slug', $request->device_slug)->first();
         $device->update([
             'elevation_mdpl' => $request->elevation_mdpl,
             'sensor_to_bank' => $request->sensor_to_bank,
             'river_depth' => $request->river_depth,
         ]);
 
         return response()->json([
             'status' => 'success',
             'message' => 'Konfigurasi kalibrasi berhasil diperbarui!',
             'config' => [
                 'elevation_mdpl' => $device->elevation_mdpl,
                 'sensor_to_bank' => $device->sensor_to_bank,
                 'river_depth' => $device->river_depth,
             ]
         ]);
     }
 
     public function latest($slug = 'cybernova-s400-primary')
    {
        $device = \App\Models\Device::where('slug', $slug)->first();

        if (!$device) {
            return response()->json([
                'status' => 'success', // Kept as success for mobile app compatibility sometimes, or check logic
                'message' => 'Device not found',
                'data' => null
            ], 404);
        }

        $data = SensorData::where('device_id', $device->id)->latest()->first();

        if (!$data) {
            return response()->json([
                'status' => 'success',
                'message' => 'No sensor data found',
                'data' => null
            ], 200); // Return 200 with null data is often safer for mobile
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Latest sensor data retrieved successfully',
            'data' => $data,
            'config' => [
                'elevation_mdpl' => $device->elevation_mdpl ?? 14.00,
                'sensor_to_bank' => $device->sensor_to_bank ?? 100,
                'river_depth' => $device->river_depth ?? 100,
            ]
        ]);
    }

    public function list()
    {
        $devices = \App\Models\Device::all()->map(function($device) {
            $latestData = SensorData::where('device_id', $device->id)->latest()->first();
            
            // Basic Status Logic (Matching web dashboard)
            $status = 'Aman';
            if ($latestData) {
                $distToBank = $device->sensor_to_bank - $latestData->distance;
                if ($distToBank >= 0) $status = 'Siaga 1';
                else if ($distToBank >= -20) $status = 'Siaga 2';
                else if ($distToBank >= -50) $status = 'Siaga 3';
            }

            return array_merge($device->toArray(), [
                'latest_distance' => $latestData->distance ?? null,
                'siaga_status' => $device->status === 'online' ? $status : 'Offline',
                'last_update' => $latestData ? $latestData->created_at->toIso8601String() : null
            ]);
        });

        return response()->json([
            'status' => 'success',
            'data' => $devices
        ]);
    }

    public function stats($slug)
    {
        $device = \App\Models\Device::where('slug', $slug)->first();
        
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Device not found'], 404);
        }

        $stats = SensorData::where('device_id', $device->id)
            ->where('created_at', '>=', now()->subDay())
            ->selectRaw('AVG(distance) as avg, MIN(distance) as min, MAX(distance) as max')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'avg_distance' => round($stats->avg ?? 0, 2),
                'min_distance' => round($stats->min ?? 0, 2),
                'max_distance' => round($stats->max ?? 0, 2),
            ]
        ]);
    }
}
