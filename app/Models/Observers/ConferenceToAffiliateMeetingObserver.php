<?php

namespace App\Models\Observers;

use Betta\Models\ConferenceToAffiliateMeeting;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceToAffiliateMeetingObserver extends AbstractObserver
{

    /**
     * Listen to the Conference creating event.
     *
     * @param  ConferenceToAffiliateMeeting  $model
     * @return void
     */
    public function creating(ConferenceToAffiliateMeeting $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
	}

    /**
     * Listen to the ConferenceToAffiliateMeeting created event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function created(ConferenceToAffiliateMeeting $model)
    {

    }

    /**
     * Listen to ConferenceToAffiliateMeeting update event
     *
     * @param  ConferenceHousing $model
     * @return void
     */
    public function updated(ConferenceToAffiliateMeeting $model)
    {

    }
}