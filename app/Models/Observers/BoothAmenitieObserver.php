<?php

namespace Betta\Models\Observers;

use Betta\Models\BoothAmenitie;
use Betta\Foundation\Eloquent\AbstractObserver;

class BoothAmenitieObserver extends AbstractObserver
{
    /**
     * Listen to the BoothAmenitie creating event.
     *
     * @param  BoothAmenitie  $model
     * @return void
     */
    public function creating(BoothAmenitie $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Tier created event.
     *
     * @param  BoothAmenitie  $model
     * @return void
     */
    public function created(BoothAmenitie $model)
    {

    }
}
