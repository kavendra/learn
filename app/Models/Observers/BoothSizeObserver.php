<?php

namespace Betta\Models\Observers;

use Betta\Models\BoothSize;
use Betta\Foundation\Eloquent\AbstractObserver;

class BoothSizeObserver extends AbstractObserver
{
    /**
     * Listen to the BoothSize creating event.
     *
     * @param  BoothSize  $model
     * @return void
     */
    public function creating(BoothSize $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the BoothSize created event.
     *
     * @param  BoothSize  $model
     * @return void
     */
    public function created(BoothSize $model)
    {

    }
}
