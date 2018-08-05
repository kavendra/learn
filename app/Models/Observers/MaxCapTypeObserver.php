<?php

namespace Betta\Models\Observers;

use Betta\Models\MaxCapType;
use Betta\Foundation\Eloquent\AbstractObserver;

class MaxCapTypeObserver extends AbstractObserver
{
    /**
     * Listen to the MaxCapType creating event.
     *
     * @param  MaxCapType  $model
     * @return void
     */
    public function creating(MaxCapType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }
}
