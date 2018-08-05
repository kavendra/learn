<?php

namespace Betta\Models\Observers;

use Betta\Models\Airline;
use Betta\Foundation\Eloquent\AbstractObserver;

class AirlineObserver extends AbstractObserver
{
    /**
     * Listen to the Airline creating event.
     *
     * @param  Airline  $model
     * @return void
     */
    public function creating(Airline $model)
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
    public function created(Airline $model)
    {

    }
}
