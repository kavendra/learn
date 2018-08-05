<?php

namespace Betta\Foundation\Events;

use Betta\Models\ProgramLocation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProgramLocationEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    public $programLocation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProgramLocation $programLocation)
    {
        $this->programLocation = $programLocation;
    }
}
