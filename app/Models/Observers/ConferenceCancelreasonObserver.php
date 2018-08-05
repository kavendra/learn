<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceCancelreason;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceCancelreasonObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceCancelreason creating event.
     *
     * @param  ConferenceCancelreason  $model
     * @return void
     */
    public function creating(ConferenceCancelreason $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceCancelreason created event.
     *
     * @param  ConferenceCancelreason  $model
     * @return void
     */
    public function created(ConferenceCancelreason $model)
    {

    }
}
