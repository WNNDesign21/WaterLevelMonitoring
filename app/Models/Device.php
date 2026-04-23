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
}
