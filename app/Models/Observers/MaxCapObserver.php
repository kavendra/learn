<?php

namespace Betta\Models\Observers;

use Betta\Models\MaxCap;
use Betta\Models\MaxCapStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class MaxCapObserver extends AbstractObserver
{
    /**
     * Create the List of MaxCap events
     *
     * @var Array
     */
    protected $statusEvents = [
        MaxCapStatus::ALLOWS_INCREASE => 'App\Events\MaxCap\AllowsIncrease',
        MaxCapStatus::PENDING_INCREASE => 'App\Events\MaxCap\PendingIncrease',
        MaxCapStatus::NO_INCREASE => 'App\Events\MaxCap\NoIncrease',
    ];

    /**
     * Name of the Field holding Status
     *
     * @var string
     */
    protected $statusField = 'max_cap_status_id';

    /**
     * Listen to the MaxCap creating() event.
     *
     * @param  Profile  $model
     * @return void
     */
    public function creating(MaxCap $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the MaxCap saved() event.
     *
     * @param  Profile  $model
     * @return void
     */
    public function saved(MaxCap $model)
    {
        if($model->isDirty($this->statusField)){
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Fire events
     *
     * @param  MaxCap $model
     * @return Void
     */
    protected function fireStatusEvents(MaxCap $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($this->statusField);

        # we have next status
        $to_status_id = $model->getAttribute($this->statusField);

        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $to_status_id)){
            event (new $event($model));
        }
    }
}
