<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileGroup;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileGroupObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileGroup creating event.
     *
     * @param  ProfileGroup  $model
     * @return void
     */
    public function creating(ProfileGroup $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProfileGroup created event.
     *
     * @param  ProfileGroup  $model
     * @return void
     */
    public function created(ProfileGroup $model)
    {

    }
}
