<?php

namespace Betta\Foundation\Events;

use Betta\Models\MaxCap;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractMaxCapEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\MaxCap
     */
    public $maxCap;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MaxCap $maxCap)
    {
        $this->maxCap = $maxCap;
    }
}
