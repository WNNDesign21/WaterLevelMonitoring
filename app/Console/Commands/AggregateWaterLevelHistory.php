<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AggregateWaterLevelHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:aggregate-water-level-history';
    protected $description = 'Aggregate raw sensor data into hourly history records';

    public function handle()
    {
        $this->info('Starting water level aggregation...');

        $now = now();
        $startTime = $now->copy()->subHour()->startOfHour();
        $endTime = $now->copy()->subHour()->endOfHour();

        $devices = \App\Models\Device::all();

        foreach ($devices as $device) {
            $this->info("Processing device: {$device->name}");

            $stats = \App\Models\SensorData::where('device_id', $device->id)
                ->whereBetween('created_at', [$startTime, $endTime])
                ->selectRaw('AVG(distance) as avg_distance, MAX(distance) as max_distance, MIN(distance) as min_distance')
                ->first();

            if ($stats && $stats->avg_distance !== null) {
                // Calculate TMA based on device elevation
                $elevation = $device->elevation_mdpl ?? 14.00;
                
                // Note: tma = elevation - (distance / 100)
                // max_tma comes from min_distance
                // min_tma comes from max_distance
                $avg_tma = $elevation - ($stats->avg_distance / 100);
                $max_tma = $elevation - ($stats->min_distance / 100);
                $min_tma = $elevation - ($stats->max_distance / 100);

                \App\Models\WaterLevelHistory::updateOrCreate(
                    [
                        'device_id' => $device->id,
                        'recorded_at' => $startTime,
                    ],
                    [
                        'avg_tma' => $avg_tma,
                        'max_tma' => $max_tma,
                        'min_tma' => $min_tma,
                        'avg_distance' => $stats->avg_distance,
                    ]
                );

                $this->info("Aggregated data for {$device->name} at {$startTime}");
            } else {
                $this->warn("No data found for {$device->name} between {$startTime} and {$endTime}");
            }
        }

        $this->info('Aggregation completed.');

        // Cleanup: Delete sensor_data older than 48 hours
        $this->info('Cleaning up old sensor data...');
        $deleted = \App\Models\SensorData::where('created_at', '<', now()->subHours(48))->delete();
        $this->info("Deleted {$deleted} old sensor records.");
    }
}
