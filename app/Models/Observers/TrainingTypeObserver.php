<?php

namespace Betta\Models\Observers;

use Betta\Models\TrainingType;
use Betta\Foundation\Eloquent\AbstractObserver;

class TrainingTypeObserver extends AbstractObserver
{
    /**
     * Listen to the TrainingType creating event.
     *
     * @param  TrainingType  $model
     * @return void
     */
    public function creating(TrainingType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the TrainingType created event.
     *
     * @param  TrainingType  $model
     * @return void
     */
    public function created(TrainingType $model)
    {

    }
}
