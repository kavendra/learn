<?php

namespace Betta\Foundation\Events;

use Betta\Models\MaxCapIncrease;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractMaxCapIncreaseEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\MaxCapIncrease
     */
    public $maxCapIncrease;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MaxCapIncrease $maxCapIncrease)
    {
        $this->maxCapIncrease = $maxCapIncrease;
    }
}
