<?php

namespace Betta\Foundation\Events;

use Betta\Models\Request;

abstract class AbstractRequestEvent extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Request
     */
    public $request;

    /**
     * Create a new event instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
