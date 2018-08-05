<?php

namespace Betta\Models\Observers;

use Betta\Models\Notification;
use Betta\Foundation\Eloquent\AbstractObserver;

class NotificationObserver extends AbstractObserver
{
    /**
     * Listen to the Notification creating event.
     *
     * @param  Notification  $model
     * @return void
     */
    public function creating(Notification $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Notification created event.
     *
     * @param  Notification  $model
     * @return void
     */
    public function created(Notification $model)
    {

    }
}
