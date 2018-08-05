<?php

namespace Betta\Foundation\Events;

use Betta\Models\ProgramSpeaker;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProgramSpeakerEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    public $programSpeaker;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProgramSpeaker $programSpeaker)
    {
        $this->programSpeaker = $programSpeaker;
    }
}
