<?php

namespace Betta\Models\Observers;

use Betta\Models\Territory;
use Betta\Foundation\Eloquent\AbstractObserver;

class TerritoryObserver extends AbstractObserver
{
    /**
     * Listen to the Territory creating event.
     *
     * @param  Territory  $model
     * @return void
     */
    public function creating(Territory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }

    /**
     * Listen to the Territory created event.
     *
     * @param  Territory  $model
     * @return void
     */
    public function created(Territory $model)
    {

    }
}
