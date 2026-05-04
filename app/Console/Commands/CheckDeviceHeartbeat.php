<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDeviceHeartbeat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-device-heartbeat';
    protected $description = 'Cek detak jantung perangkat, jika idle > 30 detik set ke offline';

    public function handle()
    {
        $threshold = now()->subSeconds(30);

        // Cari device yang statusnya online tapi sudah lama tidak kirim data
        $idleDevices = \App\Models\Device::where('status', 'online')
            ->where('last_seen', '<', $threshold)
            ->get();

        foreach ($idleDevices as $device) {
            $device->update(['status' => 'offline']);
            
            // Beritahu dashboard lewat WebSocket
            broadcast(new \App\Events\DeviceStatusUpdated($device));
            
            $this->info("Device {$device->name} sekarang OFFLINE.");
        }
    }
}
