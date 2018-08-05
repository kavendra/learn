<?php

namespace Betta\Models\Observers;

use Betta\Models\Training;
use Betta\Models\TrainingStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class TrainingObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = TrainingStatus::INITIATED;

    /**
     * Create the list of events
     *
     * @var Array
     */
    protected $statusEvents = [
        TrainingStatus::INITIATED => 'App\Events\Training\Initiated',
        TrainingStatus::INCOMPLETE => 'App\Events\Training\Incomplete',
        TrainingStatus::COMPLETED => 'App\Events\Training\Completed',
        TrainingStatus::EXPIRED => 'App\Events\Training\Expired',
        TrainingStatus::INVALID => 'App\Events\Training\Invalid',
        TrainingStatus::CANCELLED => 'App\Events\Training\Cancelled',
        TrainingStatus::INVITED => 'App\Events\Training\Invited',
        TrainingStatus::DECLINED => 'App\Events\Training\Declined',
        TrainingStatus::DID_NOT_ATTEND => 'App\Events\Training\DidNotAttend',
    ];


    /**
     * Listen to the Training creating event.
     *
     * @param  Training  $model
     * @return void
     */
    public function creating(Training $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # set Initial Status
        $model->setAttribute('training_status_id', $model->getAttribute('training_status_id') ?: $this->initialState );
    }


    /**
     * Listen to the change in status
     *
     * @param  Training  $model
     * @return void
     */
    public function saved(Training $model)
    {
        if($model->isDirty('training_status_id')){
            $this->fireStatusEvents($model);
        }
    }


    /**
     * Fire events
     *
     * @param  Training $model
     * @return Void
     */
    protected function fireStatusEvents(Training $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal('training_status_id');

        # we have next status
        $to_status_id = $model->getAttribute('training_status_id');

        # Save History
        $model->histories()->create(compact('from_status_id','to_status_id'));

        # Map the status to events and fire them all
        if ($event = array_get($this->getStatusEvents($model), $to_status_id)){
            event (new $event($model));
        }
    }


    /**
     * Decide what events should take place
     *
     * @param  Training $model
     * @return Array
     */
    protected function getStatusEvents(Training $model)
    {
        return  $this->statusEvents;
    }
}
