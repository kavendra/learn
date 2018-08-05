<?php

namespace Betta\Models\Observers;

use Betta\Models\SalesForceGroup;
use Betta\Foundation\Eloquent\AbstractObserver;

class SalesForceGroupObserver extends AbstractObserver
{
    /**
     * Listen to the SalesForceGroup creating event.
     *
     * @param  SalesForceGroup  $model
     * @return void
     */
    public function creating(SalesForceGroup $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the SalesForceGroup created event.
     *
     * @param  SalesForceGroup  $model
     * @return void
     */
    public function created(SalesForceGroup $model)
    {

    }
}
