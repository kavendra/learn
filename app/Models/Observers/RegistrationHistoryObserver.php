<?php

namespace Betta\Models\Observers;

use Betta\Models\RegistrationHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class RegistrationHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the RegistrationHistory creating event.
     *
     * @param  RegistrationHistory  $model
     * @return void
     */
    public function creating(RegistrationHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the RegistrationHistory created event.
     *
     * @param  RegistrationHistory  $model
     * @return void
     */
    public function created(RegistrationHistory $model)
    {

    }
}
