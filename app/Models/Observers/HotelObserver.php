<?php

namespace Betta\Models\Observers;

use Betta\Models\Hotel;
use Betta\Foundation\Eloquent\AbstractObserver;

class HotelObserver extends AbstractObserver
{
    /**
     * Listen to the Airline creating event.
     *
     * @param  Hotel  $model
     * @return void
     */
    public function creating(Hotel $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Airline created event.
     *
     * @param  Airline  $profile
     * @return void
     */
    public function created(Hotel $model)
    {

    }
}
