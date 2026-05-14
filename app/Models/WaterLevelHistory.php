<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterLevelHistory extends Model
{
    protected $fillable = [
        'device_id',
        'avg_tma',
        'max_tma',
        'min_tma',
        'avg_distance',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
