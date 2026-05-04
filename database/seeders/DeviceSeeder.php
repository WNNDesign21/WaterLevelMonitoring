<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Device::create([
            'name' => 'Cybernova S-400 Primary',
            'slug' => 'cybernova-s400-primary',
            'type' => 'Ultrasonic Level Sensor',
            'location' => 'Sektor Utama A-1',
            'serial_number' => 'CN-ULTR-2026-X1',
            'status' => 'online',
            'description' => 'Sistem monitoring tingkat air utama berbasis sensor ultrasonik industrial dengan akurasi millimeter.',
            'image_path' => 'devices/ultrasonic_main.png',
            'latitude' => -6.2088000,
            'longitude' => 106.8456000,
            'elevation_mdpl' => 14.00,
            'sensor_to_bank' => 100, // 1 Meter
            'river_depth' => 100,    // 1 Meter
            'specs' => [
                'Range' => '20cm - 450cm',
                'Precision' => '±2mm',
                'Frequency' => '40kHz',
                'Input Voltage' => '5V DC',
                'Material' => 'Industrial Plastic + Metal'
            ],
            'components' => [
                [
                    'id' => 'comp-mega',
                    'name' => 'Arduino Mega 2560 R3',
                    'type' => 'Core Microcontroller',
                    'image' => 'devices/arduino_mega.png',
                    'specs' => [
                        'Clock Speed' => '16 MHz',
                        'Storage' => '256 KB Flash',
                        'I/O Pins' => '54 Digital, 16 Analog'
                    ],
                    'metrics' => [
                        'voltage' => '5.01V',
                        'current' => '45mA',
                        'temp' => '38.4°C'
                    ]
                ],
                [
                    'id' => 'comp-jsn',
                    'name' => 'JSN-SR04T',
                    'type' => 'Ultrasonic Transceiver',
                    'image' => 'devices/jsn_sr04t.png',
                    'specs' => [
                        'Probe Angle' => '< 50°',
                        'Beam' => 'Point-to-Point',
                        'Protection' => 'IP66 Waterproof'
                    ],
                    'metrics' => [
                        'signal_strength' => '98%',
                        'noise_floor' => '-112dBm',
                        'response_time' => '12ms'
                    ]
                ]
            ],
            'last_seen' => now(),
        ]);
    }
}
