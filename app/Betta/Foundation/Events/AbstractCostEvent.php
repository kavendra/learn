<?php

namespace Betta\Foundation\Events;

use Betta\Models\Cost;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractCostEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Cost
     */
    public $cost;

    /**
     * Create a new event instance.
     *
     * @param Cost $cost
     */
    public function __construct(Cost $cost)
    {
        $this->cost = $cost;
    }
}
