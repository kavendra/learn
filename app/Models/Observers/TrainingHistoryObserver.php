<?php

namespace Betta\Models\Observers;

use Betta\Models\TrainingHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class TrainingHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the TrainingHistory creating event.
     *
     * @param  TrainingHistory  $model
     * @return void
     */
    public function creating(TrainingHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the TrainingHistory created event.
     *
     * @param  TrainingHistory  $model
     * @return void
     */
    public function created(TrainingHistory $model)
    {

    }
}
