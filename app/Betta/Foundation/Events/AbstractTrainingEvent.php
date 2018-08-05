<?php

namespace Betta\Foundation\Events;

use Betta\Models\Training;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractTrainingEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Training
     */
    public $training;

    /**
     * Create a new event instance.
     *
     * @param Training $contract
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }
}
