<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgressionStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgressionStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ProgressionStatus creating event.
     *
     * @param  ProgressionStatus  $model
     * @return void
     */
    public function creating(ProgressionStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgressionStatus created event.
     *
     * @param  ProgressionStatus  $model
     * @return void
     */
    public function created(ProgressionStatus $model)
    {

    }
}
