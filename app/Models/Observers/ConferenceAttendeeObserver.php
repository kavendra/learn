<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceAttendee;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceAttendeeObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceAttendee creating event.
     *
     * @param  ConferenceAttendee  $model
     * @return void
     */
    public function creating(ConferenceAttendee $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceAttendee created event.
     *
     * @param  ConferenceAttendee  $model
     * @return void
     */
    public function created(ConferenceAttendee $model)
    {

    }

    /**
     * Listen to ConferenceAttendee update event
     *
     * @param  ConferenceAttendee $model
     * @return void
     */
    public function updated(ConferenceAttendee $model)
    {

    }

}
