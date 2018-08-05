<?php

namespace Betta\Foundation\Events;

use Betta\Models\Engagement;

abstract class AbstractEngagementEvent extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Engagement
     */
    public $engagement;

    /**
     * Create a new event instance.
     *
     * @param Engagement $engagement
     */
    public function __construct(Engagement $engagement)
    {
        $this->engagement = $engagement;
    }
}
