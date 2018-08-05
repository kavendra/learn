<?php

namespace Betta\Models\Observers;

use Betta\Models\Specialty;
use Betta\Foundation\Eloquent\AbstractObserver;

class SpecialtyObserver extends AbstractObserver
{
    /**
     * Listen to the Specialty creating event.
     *
     * @param  Specialty  $model
     * @return void
     */
    public function creating(Specialty $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Specialty created event.
     *
     * @param  Specialty  $model
     * @return void
     */
    public function created(Specialty $model)
    {

    }
}
