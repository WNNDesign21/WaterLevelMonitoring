<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Default IT Administrator
        User::updateOrCreate(
            ['email' => 'admin@watersense.id'],
            [
                'name' => 'WNN Administrator',
                'password' => bcrypt('password123'),
                'role' => 'Administrator IT',
                'phone' => '081234567890',
                'address' => 'Pusat Kendali WaterSense, Karawang',
                'latitude' => -6.3227,
                'longitude' => 107.3376,
                'emergency_phone' => '112',
            ]
        );

        $this->call(DeviceSeeder::class);
    }
}
