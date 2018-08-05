<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramCloseout;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramCloseoutObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramCloseout creating event.
     *
     * @param  ProgramCloseout  $model
     * @return void
     */
    public function creating(ProgramCloseout $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramCloseout created event.
     *
     * @param  ProgramCloseout  $model
     * @return void
     */
    public function created(ProgramCloseout $model)
    {

    }
}
