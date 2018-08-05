<?php

namespace Betta\Foundation\Events;

use Betta\Models\Reconciliation;

abstract class AbstractReconciliationEvent extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Reconciliation
     */
    public $reconciliation;

    /**
     * Create a new event instance.
     *
     * @param Reconciliation $reconciliation
     */
    public function __construct(Reconciliation $reconciliation)
    {
        $this->reconciliation = $reconciliation;
    }
}
