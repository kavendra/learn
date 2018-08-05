<?php

namespace Betta\Models\Observers;

use Betta\Models\OptIn as Model;
use Betta\Foundation\Eloquent\AbstractObserver;

class OptInObserver extends AbstractObserver
{
    /**
     * Listen to the OptIn creating event.
     *
     * @param  OptIn $model
     * @return void
     */
    public function creating(Model $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
