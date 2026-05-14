<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $guarded = [];

    protected $casts = [
        'specs' => 'json',
        'components' => 'json',
        'last_seen' => 'datetime',
    ];

    /**
     * Calculate TMA (Tinggi Muka Air) in MDPL
     */
    public function calculateTma($distanceCm)
    {
        // Formula: Elevation (MDPL) - ((Distance (cm) - SensorToBank (cm)) / 100)
        $elevation = $this->elevation_mdpl ?? 14.00;
        $sensorToBank = $this->sensor_to_bank ?? 100;
        
        $diffMeters = ($distanceCm - $sensorToBank) / 100;
        return $elevation - $diffMeters;
    }
}
