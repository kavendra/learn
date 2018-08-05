<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerClassification;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerClassificationObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerClassification creating event.
     *
     * @param  SpeakerClassification  $model
     * @return void
     */
    public function creating(SpeakerClassification $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current User as Creator
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );

        # Set the Expiry Date to 1 year from now
        $this->setNullableField($model, 'valid_to');
    }
}
