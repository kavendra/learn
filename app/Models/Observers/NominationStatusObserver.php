<?php

namespace Betta\Models\Observers;

use Betta\Models\NominationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class NominationStatusObserver extends AbstractObserver
{
    /**
     * Listen to the NominationStatus creating event.
     *
     * @param  NominationStatus  $model
     * @return void
     */
    public function creating(NominationStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the NominationStatus created event.
     *
     * @param  NominationStatus  $model
     * @return void
     */
    public function created(NominationStatus $model)
    {

    }
}
