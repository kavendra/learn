<?php

namespace Betta\Models\Observers;

use Betta\Models\BudgetTransaction;
use Betta\Foundation\Eloquent\AbstractObserver;

class BudgetTransactionObserver extends AbstractObserver
{
    /**
     * Listen to the BudgetTransaction creating event.
     *
     * @param  BudgetTransaction  $model
     * @return void
     */
    public function creating(BudgetTransaction $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the BudgetTransaction created event.
     *
     * @param  BudgetTransaction  $model
     * @return void
     */
    public function created(BudgetTransaction $model)
    {

    }
}
