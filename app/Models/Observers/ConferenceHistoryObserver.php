<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceHistory creating event.
     *
     * @param  ConferenceHistory  $model
     * @return void
     */
    public function creating(ConferenceHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceHistory created event.
     *
     * @param  ConferenceHistory  $model
     * @return void
     */
    public function created(ConferenceHistory $model)
    {

    }
}
