<?php

namespace Betta\Models\Observers;

use Betta\Models\BudgetTypeBrand;
use Betta\Foundation\Eloquent\AbstractObserver;

class BudgetTypeBrandObserver extends AbstractObserver
{
    /**
     * Listen to the ContractStatus creating event.
     *
     * @param  ContractStatus  $model
     * @return void
     */
    public function creating(BudgetTypeBrand $model)
    {
        # Set Current User as Creator
        
    }

    /**
     * Listen to the ContractStatus created event.
     *
     * @param  ContractStatus  $model
     * @return void
     */
    public function created(BudgetTypeBrand $model)
    {

    }
}
