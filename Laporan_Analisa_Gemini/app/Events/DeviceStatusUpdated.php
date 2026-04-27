<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeviceStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $device;

    public function __construct($device)
    {
        $this->device = $device;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('sensor-data'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'device.status.updated';
    }
}
