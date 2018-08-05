<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerBureau;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerBureauObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerBureau creating event.
     *
     * @param  SpeakerBureau  $model
     * @return void
     */
    public function creating(SpeakerBureau $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set the valid_from  to current timestamp
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );

        # Set the Expiry Date to 1 year from now
        $model->setAttribute('valid_to', $model->getAttribute('valid_to') ?: $this->now()->addYear() );
    }

    /**
     * Listen to the SpeakerBureau created event.
     *
     * @param  SpeakerBureau  $model
     * @return void
     */
    public function created(SpeakerBureau $model)
    {

    }
}
