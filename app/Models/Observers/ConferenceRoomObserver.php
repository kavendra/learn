<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceRoom;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceRoomObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceRoom creating event.
     *
     * @param  ConferenceRoom  $model
     * @return void
     */
    public function creating(ConferenceRoom $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

    }

    /**
     * Listen to the ConferenceRoom created event.
     *
     * @param  ConferenceRoom  $model
     * @return void
     */
    public function created(ConferenceRoom $model)
    {

    }

    /**
     * Listen to ConferenceRoom update event
     *
     * @param  ConferenceRoom $model
     * @return void
     */
    public function updated(ConferenceRoom $model)
    {

    }
}
