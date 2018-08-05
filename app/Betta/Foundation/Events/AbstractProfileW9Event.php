<?php

namespace Betta\Foundation\Events;

use Betta\Models\ProfileW9;

abstract class AbstractProfileW9Event extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProfileW9
     */
    public $w9;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProfileW9 $w9)
    {
        $this->w9 = $w9;
    }
}
