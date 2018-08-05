<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceStatus creating event.
     *
     * @param  ConferenceStatus  $model
     * @return void
     */
    public function creating(ConferenceStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceStatus created event.
     *
     * @param  ConferenceStatus  $model
     * @return void
     */
    public function created(ConferenceStatus $model)
    {

    }
}
