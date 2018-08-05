<?php

namespace Betta\Models\Observers;

use Betta\Models\Progression;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgressionObserver extends AbstractObserver
{
    /**
     * Listen to the Progression creating event.
     *
     * @param  Progression  $model
     * @return void
     */
    public function creating(Progression $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Progression created event.
     *
     * @param  Progression  $model
     * @return void
     */
    public function created(Progression $model)
    {

    }
}
