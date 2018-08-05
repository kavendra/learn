<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceHousingHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceHousingHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceHousing creating event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function creating(ConferenceHousingHistory $model)
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
    public function created(ConferenceHousingHistory $model)
    {

    }

    /**
     * Listen to ConferenceHousing update event
     *
     * @param  ConferenceHousing $model
     * @return void
     */
    public function updated(ConferenceHousingHistory $model)
    {

    }
}
