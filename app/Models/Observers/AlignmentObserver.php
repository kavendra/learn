<?php

namespace Betta\Models\Observers;

use Betta\Models\Alignment;
use Betta\Foundation\Eloquent\AbstractObserver;

class AlignmentObserver extends AbstractObserver
{
    /**
     * Listen to the Alignment creating event.
     *
     * @param  Alignment  $model
     * @return void
     */
    public function creating(Alignment $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current time if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the Alignment created event.
     *
     * @param  Alignment  $model
     * @return void
     */
    public function created(Alignment $model)
    {

    }
}
