<?php

namespace Betta\Models\Observers;

use Betta\Models\TravelNeed;
use Betta\Foundation\Eloquent\AbstractObserver;

class TravelNeedObserver extends AbstractObserver
{
    /**
     * Listen to the TravelNeed creating event.
     *
     * @param  TravelNeed  $model
     * @return void
     */
    public function creating(TravelNeed $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the TravelNeed created event.
     *
     * @param  TravelNeed  $model
     * @return void
     */
    public function created(TravelNeed $model)
    {

    }
}
