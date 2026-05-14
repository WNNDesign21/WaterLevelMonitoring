<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Device;
use App\Models\WaterLevelHistory;
use Carbon\Carbon;

class WaterLevelHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = Device::all();
        $now = Carbon::now()->setHour(9)->setMinute(0)->setSecond(0); // Today at 9 AM
        $startDate = $now->copy()->subMonths(6);

        $this->command->info("Generating dummy data for " . $devices->count() . " devices...");

        foreach ($devices as $device) {
            $currentDate = $startDate->copy();
            $records = [];
            
            $baseTma = $device->elevation_mdpl - 1.5; // Base level ~1.5m below sensor
            
            while ($currentDate->lte($now)) {
                // Generate a realistic TMA using sine wave + noise
                // Pola harian (pasang surut kecil) + pola bulanan (musim)
                $dayOfYear = $currentDate->dayOfYear;
                $hourOfDay = $currentDate->hour;
                
                // Seasonal trend (higher in winter/rainy season)
                $seasonalFactor = sin(($dayOfYear / 365) * 2 * M_PI) * 0.5;
                
                // Daily fluctuation
                $dailyFactor = sin(($hourOfDay / 24) * 2 * M_PI) * 0.1;
                
                // Random noise
                $noise = (rand(-100, 100) / 1000);
                
                $tma = $baseTma + $seasonalFactor + $dailyFactor + $noise;
                
                // Distance = (Elevation - TMA) * 100
                $distance = ($device->elevation_mdpl - $tma) * 100;

                $records[] = [
                    'device_id' => $device->id,
                    'avg_tma' => $tma,
                    'max_tma' => $tma + 0.05,
                    'min_tma' => $tma - 0.05,
                    'avg_distance' => $distance,
                    'recorded_at' => $currentDate->copy(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Batch insert every 500 records to avoid memory issues
                if (count($records) >= 500) {
                    WaterLevelHistory::insert($records);
                    $records = [];
                }

                $currentDate->addHour();
            }

            // Insert remaining records
            if (count($records) > 0) {
                WaterLevelHistory::insert($records);
            }

            $this->command->info("Done for device: {$device->name}");
        }
    }
}
