<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramOnHold;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramOnHoldObserver extends AbstractObserver
{

    /**
     * Listen to the ProgramOnHold creating event.
     *
     * @param  ProgramOnHold  $model
     * @return void
     */
    public function creating(ProgramOnHold $model)
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
    public function created(ProgramOnHold $model)
    {
    }
}
