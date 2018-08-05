<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerProfile;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerProfileObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerProfile creating event.
     *
     * @param  SpeakerProfile  $model
     * @return void
     */
    public function creating(SpeakerProfile $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }


    /**
     * Listen to the SpeakerProfile created event.
     *
     * @param  SpeakerProfile  $model
     * @return void
     */
    public function created(SpeakerProfile $model)
    {

    }


    /**
     * Listen to the SpeakerProfile saving event.
     *
     * @param  SpeakerProfile  $model
     * @return void
     */
    public function saving(SpeakerProfile $model)
    {
        # Clean up the Cell Phone
        $model->setAttribute('primary_phone', $this->numbersOnly($model->getAttribute('primary_phone')) );
    }
}
