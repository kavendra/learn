<?php

namespace Betta\Models\Observers;

use Betta\Models\CostItem;
use Betta\Foundation\Eloquent\AbstractObserver;

class CostItemObserver extends AbstractObserver
{
    /**
     * Listen to the CostItem creating event.
     *
     * @param  CostItem  $model
     * @return void
     */
    public function creating(CostItem $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the CostItem created event.
     *
     * @param  CostItem  $model
     * @return void
     */
    public function created(CostItem $model)
    {

    }
}
