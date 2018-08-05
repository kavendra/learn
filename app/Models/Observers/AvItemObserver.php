<?php

namespace Betta\Models\Observers;

use Betta\Models\AvItem;
use Betta\Foundation\Eloquent\AbstractObserver;

class AvItemObserver extends AbstractObserver
{
    /**
     * Listen to the AvItem creating event.
     *
     * @param  AvItem  $model
     * @return void
     */
    public function creating(AvItem $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the AvItem created event.
     *
     * @param  AvItem  $model
     * @return void
     */
    public function created(AvItem $model)
    {

    }
}
