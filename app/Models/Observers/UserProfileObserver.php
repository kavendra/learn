<?php

namespace Betta\Models\Observers;

use Betta\Models\UserProfile;
use Betta\Foundation\Eloquent\AbstractObserver;

class UserProfileObserver extends AbstractObserver
{
    /**
     * Listen to the UserProfile creating event.
     *
     * @param  UserProfile  $model
     * @return void
     */
    public function creating(UserProfile $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the UserProfile created event.
     *
     * @param  UserProfile  $model
     * @return void
     */
    public function created(UserProfile $model)
    {

    }


    /**
     * Listen to the UserProfile saving event.
     *
     * @param  UserProfile  $model
     * @return void
     */
    public function saving(UserProfile $model)
    {
        # Clean up the Cell Phone
        $model->setAttribute('primary_phone', $this->numbersOnly($model->getAttribute('primary_phone')) );
    }
}
