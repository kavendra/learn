<?php

namespace Betta\Models\Observers;

use Betta\Models\Cancellation;
use Betta\Foundation\Eloquent\AbstractObserver;

class CancellationObserver extends AbstractObserver
{
    /**
     * Listen to the Cancellation creating event.
     *
     * @param  Cancellation  $model
     * @return void
     */
    public function creating(Cancellation $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Cancelled By User
        $model->setAttribute('cancelled_by_id', $model->getAttribute('cancelled_by_id') ?: $this->getUserId() );
    }

    /**
     * Listen to the Cancellation created event.
     *
     * @param  Cancellation  $profile
     * @return void
     */
    public function created(Cancellation $model)
    {
        // We may want to fire the event {$context_type::cancelled}
    }
}
