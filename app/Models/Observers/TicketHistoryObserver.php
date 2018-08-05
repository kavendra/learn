<?php

namespace Betta\Models\Observers;

use Betta\Models\TicketHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class TicketHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceHistory creating event.
     *
     * @param  ConferenceHistory  $model
     * @return void
     */
    public function creating(TicketHistory $model)
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
    public function created(TicketHistory $model)
    {

    }
}
