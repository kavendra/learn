<?php

namespace Betta\Models\Observers;

use Betta\Models\Holiday;
use Betta\Foundation\Eloquent\AbstractObserver;

class HolidayObserver extends AbstractObserver
{
    /**
     * Listen to the Holiday creating event.
     *
     * @param  Holiday  $model
     * @return void
     */
    public function creating(Holiday $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Holiday created event.
     *
     * @param  Holiday  $model
     * @return void
     */
    public function created(Holiday $model)
    {

    }
}
