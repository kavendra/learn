<?php

namespace Betta\Models\Observers;

use Betta\Models\NprCostCategory;
use Betta\Foundation\Eloquent\AbstractObserver;

class NprCostCategoryObserver extends AbstractObserver
{
    /**
     * Listen to the NprCostCategory creating event.
     *
     * @param  NprCostCategory  $model
     * @return void
     */
    public function creating(NprCostCategory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the CostCategory created event.
     *
     * @param  CostCategory  $model
     * @return void
     */
    public function created(NprCostCategory $model)
    {

    }
}
