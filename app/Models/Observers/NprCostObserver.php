<?php

namespace Betta\Models\Observers;

use Betta\Models\NprCost;
use Betta\Foundation\Eloquent\AbstractObserver;

class NprCostObserver extends AbstractObserver
{
    /**
     * Listen to the NprCost creating event.
     *
     * @param  NprCost  $model
     * @return void
     */
    public function creating(NprCost $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        $model->setAttribute('estimate', $model->getAttribute('estimate') ?: 0 );
    }

    /**
     * Listen to the NprCost created event.
     *
     * @param  NprCost  $model
     * @return void
     */
    public function saving(NprCost $model)
    {
        # nothing to be done

    }

    /**
     * Listen to the NprCost::saved() event
     *
     * @param  NprCost   $model
     * @return Void
     */
    public function saved(NprCost $model)
    {

    }
}
