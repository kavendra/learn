<?php

namespace Betta\Models\Observers;

use Betta\Models\NprCostItem;
use Betta\Foundation\Eloquent\AbstractObserver;

class NprCostItemObserver extends AbstractObserver
{
    /**
     * Listen to the NprCostItem creating event.
     *
     * @param  NprCostItem  $model
     * @return void
     */
    public function creating(NprCostItem $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the NprCostItem created event.
     *
     * @param  NprCostItem  $model
     * @return void
     */
    public function created(NprCostItem $model)
    {

    }
}
