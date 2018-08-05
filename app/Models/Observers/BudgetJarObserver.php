<?php

namespace Betta\Models\Observers;

use Betta\Models\BudgetJar;
use Betta\Foundation\Eloquent\AbstractObserver;

class BudgetJarObserver extends AbstractObserver
{
    /**
     * Listen to the BudgetJar creating event.
     *
     * @param  BudgetJar  $model
     * @return void
     */
    public function creating(BudgetJar $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        $model->setAttribute('current_balance', $model->getAttribute('current_balance') ?: 0 );
        $model->setAttribute('limit', $model->getAttribute('limit') ?: 0 );
    }

    /**
     * Listen to the BudgetJar created event.
     *
     * @param  BudgetJar  $model
     * @return void
     */
    public function created(BudgetJar $model)
    {

    }
}
