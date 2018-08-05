<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceHousing;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceHousingObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceHousing creating event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function creating(ConferenceHousing $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceHousing created event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function created(ConferenceHousing $model)
    {

    }

    /**
     * Listen to ConferenceHousing update event
     *
     * @param  ConferenceHousing $model
     * @return void
     */
    public function updated(ConferenceHousing $model)
    {

    }
}
