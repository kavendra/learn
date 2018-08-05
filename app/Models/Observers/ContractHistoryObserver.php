<?php

namespace Betta\Models\Observers;

use Betta\Models\ContractHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContractHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ContractHistory creating event.
     *
     * @param  ContractHistory  $model
     * @return void
     */
    public function creating(ContractHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ContractHistory created event.
     *
     * @param  ContractHistory  $model
     * @return void
     */
    public function created(ContractHistory $model)
    {

    }
}
