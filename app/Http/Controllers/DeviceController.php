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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:devices,serial_number|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . uniqid();
        $validated['status'] = 'offline';

        Device::create($validated);

        return redirect()->back()->with('success', 'Perangkat berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'serial_number' => 'required|string|unique:devices,serial_number,'.$id.'|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => 'required|string|in:online,offline,maintenance'
        ]);

        if ($request->name !== $device->name) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . uniqid();
        }

        $device->update($validated);

        return redirect()->back()->with('success', 'Data perangkat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        return redirect()->back()->with('success', 'Perangkat berhasil dihapus!');
    }
}
