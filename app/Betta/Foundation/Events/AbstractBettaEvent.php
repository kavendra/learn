<?php

namespace Betta\Foundation\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractBettaEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Return now() expressed as Carbon
     *
     * @return Carbon\Carbon
     */
    protected function now()
    {
        return Carbon::now();
    }
}
