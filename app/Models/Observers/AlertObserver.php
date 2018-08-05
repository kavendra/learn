<?php

namespace Betta\Models\Observers;

use Betta\Models\Alert;
use Betta\Foundation\Eloquent\AbstractObserver;

class AlertObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(Alert $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Initial State
        $model->setAttribute('dismissing_events',
            $model->getAttribute('dismissing_events') ?: object_get($model->alertType, 'dismissing_events') );

        # Set Dismisable State
        $model->setAttribute('is_dismissable',
            $model->getAttribute('is_dismissable') ?: object_get($model->alertType, 'is_dismissable', false) );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function saved(Alert $model)
    {
        if($model->isDirty('dismissed_at')){
            $model->setAttribute('dismissed_by', $model->getAttribute('dismissed_by') ?: $this->getUserId() );
        }
    }
}
