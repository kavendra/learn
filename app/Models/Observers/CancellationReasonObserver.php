<?php

namespace Betta\Models\Observers;

use Betta\Models\CancellationReason;
use Betta\Foundation\Eloquent\AbstractObserver;

class CancellationReasonObserver extends AbstractObserver
{
    /**
     * Listen to the CancellationReason creating event.
     *
     * @param  CancellationReason  $model
     * @return void
     */
    public function creating(CancellationReason $model)
    {
    }

    /**
     * Listen to the CancellationReason created event.
     *
     * @param  CancellationReason  $profile
     * @return void
     */
    public function created(CancellationReason $model)
    {

    }
}
