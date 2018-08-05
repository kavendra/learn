<?php

namespace Betta\Models\Observers;

use Betta\Models\NominationHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class NominationHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the NominationHistory creating event.
     *
     * @param  NominationHistory  $model
     * @return void
     */
    public function creating(NominationHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the NominationHistory created event.
     *
     * @param  NominationHistory  $model
     * @return void
     */
    public function created(NominationHistory $model)
    {

    }
}
