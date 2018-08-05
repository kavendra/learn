<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerClassificationGroupProgramTypeBrand;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerClassificationGroupProgramTypeBrandObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerClassificationGroupProgramTypeBrand creating event.
     *
     * @param  SpeakerClassificationGroupProgramTypeBrand  $model
     * @return void
     */
    public function creating(SpeakerClassificationGroupProgramTypeBrand $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current User as Creator
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );

        # Set the Expiry Date to 1 year from now
        $model->setAttribute('valid_to', $model->getAttribute('valid_to') ?: $this->now()->addYear() );
    }

    /**
     * Listen to the SpeakerClassificationGroupProgramTypeBrand created event.
     *
     * @param  SpeakerClassificationGroupProgramTypeBrand  $model
     * @return void
     */
    public function created(SpeakerClassificationGroupProgramTypeBrand $model)
    {

    }
}
