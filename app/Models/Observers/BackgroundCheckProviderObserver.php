<?php

namespace Betta\Models\Observers;

use Betta\Models\BackgroundCheckProvider;
use Betta\Foundation\Eloquent\AbstractObserver;

class BackgroundCheckProviderObserver extends AbstractObserver
{
    /**
     * Listen to the BackgroundCheckProvider creating event.
     *
     * @param  BackgroundCheckProvider  $model
     * @return void
     */
    public function creating(BackgroundCheckProvider $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
