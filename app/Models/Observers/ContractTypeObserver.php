<?php

namespace Betta\Models\Observers;

use Betta\Models\ContractType;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContractTypeObserver extends AbstractObserver
{
    /**
     * Listen to the ContractType creating event.
     *
     * @param  ContractType  $model
     * @return void
     */
    public function creating(ContractType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ContractType created event.
     *
     * @param  ContractType  $model
     * @return void
     */
    public function created(ContractType $model)
    {

    }
}
