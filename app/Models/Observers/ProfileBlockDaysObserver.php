<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileBlockDays;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileBlockDaysObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileAssistant creating event.
     *
     * @param  ProfileAssistant  $model
     * @return void
     */
    public function creating(ProfileBlockDays $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
