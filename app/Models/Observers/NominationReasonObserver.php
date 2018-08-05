<?php

namespace Betta\Models\Observers;

use Betta\Models\NominationReason;
use Betta\Foundation\Eloquent\AbstractObserver;

class NominationReasonObserver extends AbstractObserver
{
    /**
     * Listen to the NominationReason creating event.
     *
     * @param  NominationReason  $model
     * @return void
     */
    public function creating(NominationReason $model)
    {
        # Void
    }

    /**
     * Listen to the NominationReason created event.
     *
     * @param  NominationReason  $profile
     * @return void
     */
    public function created(NominationReason $model)
    {
        # Void
    }
}
