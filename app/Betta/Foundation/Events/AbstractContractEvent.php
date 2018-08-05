<?php

namespace Betta\Foundation\Events;

use Betta\Models\Contract;

abstract class AbstractContractEvent extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Contract
     */
    public $contract;

    /**
     * Create a new event instance.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }
}
