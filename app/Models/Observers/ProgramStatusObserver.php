<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramStatus creating event.
     *
     * @param  ProgramStatus  $model
     * @return void
     */
    public function creating(ProgramStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramStatus created event.
     *
     * @param  ProgramStatus  $model
     * @return void
     */
    public function created(ProgramStatus $model)
    {

    }
}
