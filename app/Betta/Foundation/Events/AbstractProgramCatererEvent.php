<?php

namespace Betta\Foundation\Events;

use Betta\Models\ProgramCaterer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProgramCatererEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    public $programCaterer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProgramCaterer $programCaterer)
    {
        $this->programCaterer = $programCaterer;
    }
}
