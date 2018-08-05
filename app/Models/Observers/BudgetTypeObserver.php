<?php

namespace Betta\Models\Observers;

use Betta\Models\BudgetType;
use Betta\Foundation\Eloquent\AbstractObserver;

class BudgetTypeObserver extends AbstractObserver
{
    /**
     * Listen to the BudgetType creating event.
     *
     * @param  BudgetType  $model
     * @return void
     */
    public function creating(BudgetType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the BudgetType created event.
     *
     * @param  BudgetType  $model
     * @return void
     */
    public function created(BudgetType $model)
    {

    }
}
