<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramType;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramTypeObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramType creating event.
     *
     * @param  ProgramType  $model
     * @return void
     */
    public function creating(ProgramType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramType created event.
     *
     * @param  ProgramType  $model
     * @return void
     */
    public function created(ProgramType $model)
    {

    }
}
