<?php

namespace Betta\Models\Observers;

use Betta\Models\Degree;
use Betta\Foundation\Eloquent\AbstractObserver;

class DegreeObserver extends AbstractObserver
{
    /**
     * Listen to the Degree creating event.
     *
     * @param  Degree  $model
     * @return void
     */
    public function creating(Degree $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Degree created event.
     *
     * @param  Degree  $model
     * @return void
     */
    public function created(Degree $model)
    {

    }
}
