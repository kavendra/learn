<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileAssistant;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileAssistantObserver extends AbstractObserver
{
    /**
     * Listen to the ProfileAssistant creating event.
     *
     * @param  ProfileAssistant  $model
     * @return void
     */
    public function creating(ProfileAssistant $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
