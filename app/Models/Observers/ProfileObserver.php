<?php

namespace Betta\Models\Observers;

use Betta\Models\Profile;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileObserver extends AbstractObserver
{
    /**
     * Listen to the Profile creating event.
     *
     * @param  Profile  $model
     * @return void
     */
    public function creating(Profile $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Profile created event.
     *
     * @param  Profile  $model
     * @return void
     */
    public function created(Profile $model)
    {

    }
}
