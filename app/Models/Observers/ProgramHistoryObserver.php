<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramHistory creating event.
     *
     * @param  ProgramHistory  $model
     * @return void
     */
    public function creating(ProgramHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramHistory created event.
     *
     * @param  ProgramHistory  $model
     * @return void
     */
    public function created(ProgramHistory $model)
    {

    }
}
