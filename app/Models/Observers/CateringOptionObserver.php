<?php

namespace Betta\Models\Observers;

use Betta\Models\CateringOption;
use Betta\Foundation\Eloquent\AbstractObserver;

class CateringOptionObserver extends AbstractObserver
{
    /**
     * Listen to the CateringOption creating event.
     *
     * @param  CateringOption  $model
     * @return void
     */
    public function creating(CateringOption $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the CateringOption created event.
     *
     * @param  CateringOption  $model
     * @return void
     */
    public function created(CateringOption $model)
    {

    }
}
