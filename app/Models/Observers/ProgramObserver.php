<?php

namespace Betta\Models\Observers;

use Betta\Models\Program;
use Betta\Models\CostItem;
use Betta\Models\ProgramStatus;
use App\Events\Program\ReconcileChange;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramObserver extends AbstractObserver
{
    /**
     * Define the events
     *
     * @var Array
     */
    protected $statusEvents = [
        ProgramStatus::DRAFT => 'App\Events\Program\Created',
        ProgramStatus::SUBMITTED => 'App\Events\Program\Submitted',
        ProgramStatus::DENIED => 'App\Events\Program\Denied',
        ProgramStatus::APPROVED => 'App\Events\Program\Approved',
        ProgramStatus::PENDING_MANAGER  => 'App\Events\Program\PendingManager',
        ProgramStatus::MANAGER_APPROVED => 'App\Events\Program\ManagerApproved',
        ProgramStatus::MANAGER_DENIED => 'App\Events\Program\ManagerDenied',
        ProgramStatus::IN_PROGRESS => 'App\Events\Program\InProgress',
        ProgramStatus::CONFIRMED => 'App\Events\Program\Confirmed',
        ProgramStatus::COMPLETED => 'App\Events\Program\Completed',
        ProgramStatus::CLOSED => 'App\Events\Program\Closed',
        ProgramStatus::CLOSED_OUT => 'App\Events\Program\ClosedOut',
        ProgramStatus::CANCELLED => 'App\Events\Program\Cancelled',
    ];

    /**
     * Listen to the Program creating event.
     *
     * @param  Program  $model
     * @return void
     */
    public function creating(Program $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Program Status ID
        $model->setAttribute('program_status_id', $model->getAttribute('program_status_id') ?: $this->getInitialStatus() );
    }

    /**
     * Listen to the Program created event.
     *
     * @param  Program  $model
     * @return void
     */
    public function created(Program $model)
    {
        $model->histories()->create(['to_status_id' => $model->program_status_id]);
        # Also, fire the event
        $this->fireStatusEvents($model);
    }

    /**
     * Listen to the Program saving event.
     *
     * @param  Program  $model
     * @return void
     */
    public function saving(Program $model)
    {
        # Set Label
        $model->setAttribute('label', $model->auto_label );

        if($model->isDirty('start_date') AND !$model->isDirty('end_date')){
            $this->resetEndDate($model);
        }
    }

    /**
     * Listen to the Program saved event.
     *
     * @param  Program  $model
     * @return void
     */
    public function saved(Program $model)
    {
        # If the Program Allows Food:
        if ($model->businessRule('allow_food')){
            $this->updateFbCost($model);
        }
        # Fire reconciled event
        if($model->isDirty('is_reconciled')){
            event(new ReconcileChange($model));
        }
    }

    /**
     * Listen to Program update event
     *
     * @param  Program $model
     * @return void
     */
    public function updated(Program $model)
    {
        if($model->isDirty('program_status_id')){
            # Record Histories
            $this->recordHistories($model);
            # Fire the events
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Reset the End date
     *
     * @param  Program $model
     * @return [type]
     */
    protected function resetEndDate(Program $model)
    {
        $model->setAttribute('end_date', $this->getDefaultEndDate($model) );
    }

    /**
     * Update FB Cost
     *
     * @param  Program $model
     * @return Void
     */
    protected function updateFbCost(Program $model)
    {
        # assumption, needs to come from interface
        $fbCost = CostItem::FB;

        # update cost for the F&B
        $model->costs()->updateOrCreate(['cost_item_id'=> $fbCost], ['estimate'=>$model->estimateFbCost()] );
    }

    /**
     * Return the Initial Status
     *
     * @return int
     */
    protected function getInitialStatus()
    {
        return object_get($this, 'initial_status', ProgramStatus::DRAFT);
    }

    /**
     * Add Length of program to Start Date
     *
     * @param  Program $model
     * @return Carbon
     */
    protected function getDefaultEndDate(Program $model)
    {
        return $model->start_date->addMinutes( object_get($model->programType, 'duration', 0) );
    }

    /**
     * Fire events
     *
     * @param  Program $model
     * @return Void
     */
    protected function fireStatusEvents(Program $model)
    {
        # we have Previous status
        $previous = $model->getOriginal('program_status_id');

        # we have next status
        $next = $model->getAttribute('program_status_id');

        # storing Program Cancellation reason and Cancellation notes
        if($next == ProgramStatus::CANCELLED && $previous != $next){
             if(request()->has('cancellation_reason_id')){
                $cancellation = $model->cancellations()->create([ 'cancellation_reason_id'=>request()->input('cancellation_reason_id') ]);

                # if there are notes:
                if ( request()->has('cancellation_notes') ){
                    $cancellation->notes()->create( ['content'=>request()->input('cancellation_notes')]  );
                }
            }
        }

        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }

    /**
     * Record History Progression for the Program
     *
     * @param  Program $model
     * @return Void
     */
    protected function recordHistories(Program $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal('program_status_id');

        # we have next status
        $to_status_id = $model->getAttribute('program_status_id');

        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));
    }
}
