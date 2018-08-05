<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileBlockDates;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileBlockDatesObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileAssistant creating event.
     *
     * @param  ProfileAssistant  $model
     * @return void
     */
    public function creating(ProfileBlockDates $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
