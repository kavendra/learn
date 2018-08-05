<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileExperience;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileExperienceObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileExperience creating event.
     *
     * @param  ProfileExperience  $model
     * @return void
     */
    public function creating(ProfileExperience $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProfileExperience created event.
     *
     * @param  ProfileExperience  $model
     * @return void
     */
    public function created(ProfileExperience $model)
    {

    }
}
