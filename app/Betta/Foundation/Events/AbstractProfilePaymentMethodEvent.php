<?php

namespace Betta\Foundation\Events;

use Illuminate\Broadcasting\Channel;
use Betta\Models\ProfilePaymentMethod;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractProfilePaymentMethodEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProfilePaymentMethod
     */
    public $paymentMethod;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProfilePaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }
}
