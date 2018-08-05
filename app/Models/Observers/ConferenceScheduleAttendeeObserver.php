<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceScheduleAttendee;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceScheduleAttendeeObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceScheduleAttendee creating event.
     *
     * @param  ConferenceScheduleAttendee  $model
     * @return void
     */
    public function creating(ConferenceScheduleAttendee $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceScheduleAttendee created event.
     *
     * @param  ConferenceScheduleAttendee  $model
     * @return void
     */
    public function created(ConferenceScheduleAttendee $model)
    {

    }
}
