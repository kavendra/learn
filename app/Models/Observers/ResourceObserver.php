<?php

namespace Betta\Models\Observers;

use Betta\Models\Resource;
use Betta\Foundation\Eloquent\AbstractObserver;

class ResourceObserver extends AbstractObserver
{
    /**
     * Listen to the Resource creating event.
     *
     * @param  Resource  $model
     * @return void
     */
    public function creating(Resource $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Resource created event.
     *
     * @param  Resource  $model
     * @return void
     */
    public function created(Resource $model)
    {

    }
}
