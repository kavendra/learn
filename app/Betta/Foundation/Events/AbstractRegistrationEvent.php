<?php

namespace Betta\Foundation\Events;

use Betta\Models\Registration;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractRegistrationEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Registration
     */
    public $registration;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }
}
