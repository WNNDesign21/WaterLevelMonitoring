<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('devices.index', compact('devices'));
    }

    public function show($slug)
    {
        $device = Device::where('slug', $slug)->firstOrFail();
        return view('devices.show', compact('device'));
    }

    public function updateLocation(Request $request, $slug)
    {
        $device = Device::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $device->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        event(new \App\Events\DeviceLocationUpdated($device));

        return response()->json([
            'success' => true,
            'message' => 'Lokasi perangkat berhasil diperbarui!',
            'latitude' => $device->latitude,
            'longitude' => $device->longitude,
        ]);
    }
}
