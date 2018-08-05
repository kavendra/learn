<?php

namespace Betta\Models\Observers;

use Betta\Models\CostCategory;
use Betta\Foundation\Eloquent\AbstractObserver;

class CostCategoryObserver extends AbstractObserver
{
    /**
     * Listen to the CostCategory creating event.
     *
     * @param  CostCategory  $model
     * @return void
     */
    public function creating(CostCategory $model)
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
    public function created(CostCategory $model)
    {

    }
}
