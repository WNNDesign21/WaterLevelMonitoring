<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensorData;
    public $config;
    public $slug;

    /**
     * Create a new event instance.
     */
    public function __construct($sensorData, $device)
    {
        $this->sensorData = $sensorData;
        $this->slug = $device->slug;
        $this->config = [
            'elevation_mdpl' => $device->elevation_mdpl ?? 14.00,
            'sensor_to_bank' => $device->sensor_to_bank ?? 100,
            'river_depth' => $device->river_depth ?? 100,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('sensor-data.' . $this->slug),
            new Channel('sensor-data'), // Keep backward compatibility if needed
        ];
    }

    public function broadcastAs(): string
    {
        return 'sensor.updated';
    }
}
