<?php

namespace Betta\Models\Observers;

use Betta\Models\RegistrationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class RegistrationStatusObserver extends AbstractObserver
{
    /**
     * Listen to the RegistrationStatus creating event.
     *
     * @param  RegistrationStatus  $model
     * @return void
     */
    public function creating(RegistrationStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the RegistrationStatus created event.
     *
     * @param  RegistrationStatus  $model
     * @return void
     */
    public function created(RegistrationStatus $model)
    {

    }
}
