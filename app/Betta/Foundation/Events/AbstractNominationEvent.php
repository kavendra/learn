<?php

namespace Betta\Foundation\Events;

use Betta\Models\Nomination;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractNominationEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Nomination
     */
    public $nomination;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Nomination $nomination)
    {
        $this->nomination = $nomination;
    }
}
