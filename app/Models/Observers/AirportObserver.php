<?php

namespace Betta\Models\Observers;

use Betta\Models\Airport;
use Betta\Foundation\Eloquent\AbstractObserver;

class AirportObserver extends AbstractObserver
{
    /**
     * Listen to the Airport creating event.
     *
     * @param  Airline  $model
     * @return void
     */
    public function creating(Airport $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Airline created event.
     *
     * @param  Airport  $model
     * @return void
     */
    public function created(Airport $model)
    {

    }
}
