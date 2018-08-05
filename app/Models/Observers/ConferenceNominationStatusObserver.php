<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceNominationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceNominationStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceNominationStatus creating event.
     *
     * @param  ConferenceNominationStatus  $model
     * @return void
     */
    public function creating(ConferenceNominationStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceNominationStatus created event.
     *
     * @param  ConferenceNominationStatus  $model
     * @return void
     */
    public function created(ConferenceNominationStatus $model)
    {

    }
}
