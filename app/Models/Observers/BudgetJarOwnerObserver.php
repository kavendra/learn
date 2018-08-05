<?php

namespace Betta\Models\Observers;

use Betta\Models\BudgetJarOwner;
use Betta\Foundation\Eloquent\AbstractObserver;

class BudgetJarOwnerObserver extends AbstractObserver
{
    /**
     * Listen to the BudgetJarOwner creating event.
     *
     * @param  BudgetJarOwner  $model
     * @return void
     */
    public function creating(BudgetJarOwner $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the BudgetJarOwner created event.
     *
     * @param  BudgetJarOwner  $model
     * @return void
     */
    public function created(BudgetJarOwner $model)
    {

    }
}
