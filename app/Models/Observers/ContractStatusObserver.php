<?php

namespace Betta\Models\Observers;

use Betta\Models\ContractStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContractStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ContractStatus creating event.
     *
     * @param  ContractStatus  $model
     * @return void
     */
    public function creating(ContractStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ContractStatus created event.
     *
     * @param  ContractStatus  $model
     * @return void
     */
    public function created(ContractStatus $model)
    {

    }
}
