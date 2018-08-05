<?php

namespace Betta\Models\Observers;

use Betta\Models\AlignmentBrand;
use Betta\Foundation\Eloquent\AbstractObserver;

class AlignmentBrandObserver extends AbstractObserver
{
    /**
     * Listen to the AlignmentBrand creating event.
     *
     * @param  AlignmentBrand  $model
     * @return void
     */
    public function creating(AlignmentBrand $model)
    {
        # Set Current time if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the AlignmentBrand created event.
     *
     * @param  AlignmentBrand  $model
     * @return void
     */
    public function created(AlignmentBrand $model)
    {

    }
}
