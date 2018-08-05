<?php

namespace Betta\Models\Observers;

use Betta\Models\AlertType;
use Betta\Foundation\Eloquent\AbstractObserver;

class AlertTypeObserver extends AbstractObserver
{
    protected $initial_state = 'info';

    /**
     * Listen to the AlertType creating event.
     *
     * @param  AlertType  $model
     * @return void
     */
    public function creating(AlertType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Initial State
        $model->setAttribute('initial_state', $model->getAttribute('initial_state') ?: $this->initial_state );
    }

    /**
     * Listen to the AlertType created event.
     *
     * @param  AlertType  $model
     * @return void
     */
    public function created(AlertType $model)
    {

    }
}
