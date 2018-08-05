<?php

namespace Betta\Models\Observers;

use Betta\Models\TrainingStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class TrainingStatusObserver extends AbstractObserver
{
    /**
     * Listen to the TrainingStatus creating event.
     *
     * @param  TrainingStatus  $model
     * @return void
     */
    public function creating(TrainingStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the TrainingStatus created event.
     *
     * @param  TrainingStatus  $model
     * @return void
     */
    public function created(TrainingStatus $model)
    {

    }
}
