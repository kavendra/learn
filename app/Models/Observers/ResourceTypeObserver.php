<?php

namespace Betta\Models\Observers;

use Betta\Models\ResourceType;
use Betta\Foundation\Eloquent\AbstractObserver;

class ResourceTypeObserver extends AbstractObserver
{
    /**
     * Listen to the ResourceType creating event.
     *
     * @param  ResourceType  $model
     * @return void
     */
    public function creating(ResourceType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ResourceType created event.
     *
     * @param  ResourceType  $model
     * @return void
     */
    public function created(ResourceType $model)
    {

    }
}
