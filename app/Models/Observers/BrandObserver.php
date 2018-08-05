<?php

namespace Betta\Models\Observers;

use Betta\Models\Brand;
use Betta\Foundation\Eloquent\AbstractObserver;

class BrandObserver extends AbstractObserver
{
    /**
     * Listen to the Brand creating event.
     *
     * @param  Brand  $model
     * @return void
     */
    public function creating(Brand $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current time as Valid_from if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the Brand created event.
     *
     * @param  Brand  $model
     * @return void
     */
    public function created(Brand $model)
    {

    }
}
