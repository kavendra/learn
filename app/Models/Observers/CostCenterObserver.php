<?php

namespace Betta\Models\Observers;

use Betta\Models\CostCenter;
use Betta\Foundation\Eloquent\AbstractObserver;

class CostCenterObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramHistory creating event.
     *
     * @param  CostCenter  $model
     * @return void
     */
    public function creating(CostCenter $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the CostCenter created event.
     *
     * @param  CostCenter  $model
     * @return void
     */
    public function created(CostCenter $model)
    {

    }
}
