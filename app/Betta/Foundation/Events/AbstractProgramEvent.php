<?php

namespace Betta\Foundation\Events;

use Betta\Models\Program;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProgramEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    public $program;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }
}
