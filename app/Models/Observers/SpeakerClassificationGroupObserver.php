<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerClassificationGroup;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerClassificationGroupObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerClassificationGroup creating event.
     *
     * @param  SpeakerClassificationGroup  $model
     * @return void
     */
    public function creating(SpeakerClassificationGroup $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current User as Creator
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );

        # Set the Expiry Date to 1 year from now
        $model->setAttribute('valid_to', $model->getAttribute('valid_to') ?: $this->now()->addYear() );
    }

    /**
     * Listen to the SpeakerClassificationGroup created event.
     *
     * @param  SpeakerClassificationGroup  $model
     * @return void
     */
    public function created(SpeakerClassificationGroup $model)
    {

    }
}
