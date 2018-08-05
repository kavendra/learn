<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceSchedule;
use Betta\Models\ConferenceScheduleStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceScheduleObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceSchedule creating event.
     *
     * @param  ConferenceSchedule  $model
     * @return void
     */
    public function creating(ConferenceSchedule $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        $model->setAttribute('schedule_status_id', $model->getAttribute('schedule_status_id') ?: ConferenceScheduleStatus::REQUESTED );

        $model->setAttribute('meeting_description', $model->getAttribute('meeting_description') ?: "" );

        $this->setHost($model);

    }

    /**
     * Listen to the ConferenceSchedule created event.
     *
     * @param  ConferenceSchedule  $model
     * @return void
     */
    public function created(ConferenceSchedule $model)
    {

    }

    /**
     * Listen to the ConferenceSchedule created event.
     *
     * @param  ConferenceSchedule  $model
     * @return void
     */

    protected function setHost($model)
    {
        # Set Creator as Host
        $model->setAttribute('host_name', $model->getAttribute('host_name') ?: data_get($this->getUser(), 'profile.preferred_name') );

        $model->setAttribute('host_email', $model->getAttribute('host_email') ?: data_get($this->getUser(), 'profile.primary_email') );

        $model->setAttribute('host_mobile', $model->getAttribute('host_mobile') ?: data_get($this->getUser(), 'profile.dial_in_phone_number') );

        $model->setAttribute('host_title', $model->getAttribute('host_title') ?: data_get($this->getUser(), 'profile.title') );

    }

}
