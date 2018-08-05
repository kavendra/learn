<?php

namespace Betta\Models\Observers;

use Betta\Models\SpeakerToAttestation;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpeakerToAttestationObserver extends AbstractObserver
{
    /**
     * Listen to the SpeakerToAttestation creating event.
     *
     * @param  AvItem  $model
     * @return void
     */
    public function creating(SpeakerToAttestation $model)
    {
        # Set Current User as Creator
       
    }

    /**
     * Listen to the SpeakerToAttestation created event.
     *
     * @param  AvItem  $model
     * @return void
     */
    public function created(SpeakerToAttestation $model)
    {

    }
}
