<?php

namespace Betta\Models\Observers;

use Betta\Models\NotificationType;
use Betta\Foundation\Eloquent\AbstractObserver;

class NotificationTypeObserver extends AbstractObserver
{
    protected $initial_state = 'info';

    /**
     * Listen to the NotificationType creating event.
     *
     * @param  NotificationType  $model
     * @return void
     */
    public function creating(NotificationType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Initial State
        $model->setAttribute('initial_state', $model->getAttribute('initial_state') ?: $this->initial_state );
    }

    /**
     * Listen to the NotificationType created event.
     *
     * @param  NotificationType  $model
     * @return void
     */
    public function created(NotificationType $model)
    {

    }
}
