<?php

namespace Betta\Foundation\Events;

use Betta\Models\Conference;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractConferenceEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    public $conference;

    /**
     * Create a new event instance.
     *
     * @param  Betta\Models\Conference $conference
     * @return void
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }
}
