<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceScheduleStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceScheduleStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceScheduleStatus creating event.
     *
     * @param  ConferenceScheduleStatus  $model
     * @return void
     */
    public function creating(ConferenceScheduleStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceScheduleStatus created event.
     *
     * @param  ConferenceScheduleStatus  $model
     * @return void
     */
    public function created(ConferenceScheduleStatus $model)
    {

    }
}
