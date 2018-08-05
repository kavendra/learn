<?php

namespace Betta\Models\Observers;

use Betta\Models\RepProfile;
use Betta\Foundation\Eloquent\AbstractObserver;

class RepProfileObserver extends AbstractObserver
{
    /**
     * Listen to the RepProfile creating event.
     *
     * @param  RepProfile  $model
     * @return void
     */
    public function creating(RepProfile $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the RepProfile created event.
     *
     * @param  RepProfile  $model
     * @return void
     */
    public function created(RepProfile $model)
    {

    }


    /**
     * Listen to the RepProfile saving event.
     *
     * @param  RepProfile  $model
     * @return void
     */
    public function saving(RepProfile $model)
    {
        # Clean up the Cell Phone
        $model->setAttribute('primary_phone', $this->numbersOnly($model->getAttribute('primary_phone')) );
    }
}
