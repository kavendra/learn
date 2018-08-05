<?php

namespace Betta\Models\Observers;

use Betta\Models\DisplayType;
use Betta\Foundation\Eloquent\AbstractObserver;

class DisplayTypeObserver extends AbstractObserver
{
    /**
     * Listen to the DisplayType creating event.
     *
     * @param  DisplayType  $model
     * @return void
     */
    public function creating(DisplayType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the DisplayType created event.
     *
     * @param  DisplayType  $model
     * @return void
     */
    public function created(DisplayType $model)
    {

    }
}
