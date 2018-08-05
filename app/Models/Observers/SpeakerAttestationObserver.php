<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerAttestation;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerAttestationObserver extends AbstractObserver
{
    /**
     * Listen to the BudgetJar creating event.
     *
     * @param  BudgetJar  $model
     * @return void
     */
    public function creating(SpeakerAttestation $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

}
