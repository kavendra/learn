<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceOnHold;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceOnHoldObserver extends AbstractObserver
{

    /**
     * Listen to the ProgramOnHold creating event.
     *
     * @param  ProgramOnHold  $model
     * @return void
     */
    public function creating(ConferenceOnHold $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramOnHold created event.
     *
     * @param  ProgramOnHold  $model
     * @return void
     */
    public function created(ConferenceOnHold $model)
    {
    }
}
