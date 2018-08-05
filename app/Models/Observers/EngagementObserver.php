<?php

namespace Betta\Models\Observers;

use Betta\Models\Engagement;
use Betta\Models\EngagementStatus as Status;
use Betta\Foundation\Eloquent\AbstractObserver;

class EngagementObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = Status::DRAFT;

    /**
     * Create the list of events
     *
     * @var Array
     */
    protected $statusEvents = [
        Status::OPEN => 'App\Events\Engagement\Open',
        Status::COMPLETED => 'App\Events\Engagement\Completed',
        Status::CLOSED_OUT => 'App\Events\Engagement\ClosedOut',
        Status::CANCELLED => 'App\Events\Engagement\Cancelled',
    ];

    /**
     * Listen to the Engagement creating event.
     *
     * @param  Engagement  $model
     * @return void
     */
    public function creating(Engagement $model)
    {
        # Add Creator, if not set
        $this->setCreator($model);
        # set Initial Status
        $model->setAttribute(
            $model->getStatusFieldName(),
            $model->getAttribute($model->getStatusFieldName()) ?: $this->initialState
        );
    }

    /**
     * Listen to the Saved Event
     *
     * @param  Engagement  $model
     * @return void
     */
    public function saved(Engagement $model)
    {
        if($model->isDirty($model->getStatusFieldName())){
            $this->recordHistory($model);
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Fire events
     *
     * @param  Engagement $model
     * @return Void
     */
    protected function recordHistory(Engagement $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($model->getStatusFieldName());
        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());
        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));
    }

    /**
     * Fire events
     *
     * @param  Engagement $model
     * @return Void
     */
    protected function fireStatusEvents(Engagement $model)
    {
        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());
        # Map the status to events and fire them all
        if ($event = array_get($this->getStatusEvents($model), $to_status_id)){
            event (new $event($model));
        }
    }

    /**
     * Decide what events should take palce
     *
     * @param  Engagement $model
     * @return Array
     */
    protected function getStatusEvents(Engagement $model)
    {
        return  $this->statusEvents;
    }
}
