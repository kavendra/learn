<?php

namespace Betta\Models\Observers;

use Betta\Models\Tier;
use Betta\Foundation\Eloquent\AbstractObserver;

class TierObserver extends AbstractObserver
{
    /**
     * Listen to the Tier creating event.
     *
     * @param  Tier  $model
     * @return void
     */
    public function creating(Tier $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Tier created event.
     *
     * @param  Tier  $model
     * @return void
     */
    public function created(Tier $model)
    {

    }
}
