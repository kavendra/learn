<?php

namespace Betta\Models\Observers;

use Betta\Models\Reference;
use Betta\Foundation\Eloquent\AbstractObserver;

class ReferenceObserver extends AbstractObserver
{
    /**
     * Listen to the Reference creating event.
     *
     * @param  Reference  $model
     * @return void
     */
    public function creating(Reference $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current time if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the Reference created event.
     *
     * @param  Reference  $model
     * @return void
     */
    public function created(Reference $model)
    {

    }
}
