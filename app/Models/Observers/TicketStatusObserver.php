<?php

namespace Betta\Models\Observers;

use Betta\Models\TicketStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class TicketStatusObserver extends AbstractObserver
{
    /**
     * Listen to the TicketStatus creating event.
     *
     * @param  TicketStatus  $model
     * @return void
     */
    public function creating(TicketStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
