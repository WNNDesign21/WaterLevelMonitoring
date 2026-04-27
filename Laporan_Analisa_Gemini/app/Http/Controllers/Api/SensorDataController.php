<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Events\SensorDataReceived;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'distance' => 'required|numeric',
            'valid_count' => 'nullable|integer'
        ]);

        $data = SensorData::create([
            'distance' => $request->distance,
            'valid_count' => $request->valid_count ?? 1,
        ]);

        // Update Device Status (Assuming Primary for this endpoint)
        \App\Models\Device::where('slug', 'cybernova-s400-primary')->update([
            'status' => 'online',
            'last_seen' => now()
        ]);

        // Broadcast the event with the new data
        broadcast(new SensorDataReceived($data));

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
    public function latest()
    {
        $data = SensorData::latest()->first();

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'No sensor data found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Latest sensor data retrieved successfully',
            'data' => $data
        ]);
    }
}
