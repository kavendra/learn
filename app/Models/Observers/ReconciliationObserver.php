<?php

namespace Betta\Models\Observers;

use Betta\Models\Reconciliation;
use Betta\Models\ReconciliationStatus as Status;
use Betta\Foundation\Eloquent\AbstractObserver as Observer;

class ReconciliationObserver extends Observer
{
    /**
     * Define the events
     *
     * @var Array
     */
    protected $statusEvents = [
        Status::CLOSEOUT_INITIATED => 'App\Events\Program\Reconciliation\CloseoutInitiated',
        Status::CLOSEOUT_IN_PROGRESS => 'App\Events\Program\Reconciliation\CloseoutInProgress',
        Status::CLOSEOUT_COMPLETE => 'App\Events\Program\Reconciliation\CloseoutComplete',
        Status::CLOSEOUT_REJECTED => 'App\Events\Program\Reconciliation\CloseoutRejected',
        Status::RECONCILIATION_INITIATED => 'App\Events\Program\Reconciliation\ReconciliationInitiated',
        Status::RECONCILIATION_COMPLETE => 'App\Events\Program\Reconciliation\ReconciliationComplete',
        Status::RECONCILIATION_REJECTED => 'App\Events\Program\Reconciliation\ReconciliationRejected',
        Status::FINAL_RECONCILIATION_IN_PROGRESS => 'App\Events\Program\Reconciliation\FinalReconciliationInProgress',
        Status::FINAL_RECONCILIATION_COMPLETE => 'App\Events\Program\Reconciliation\FinalReconciliation',
    ];

    /**
     * Listen to the model's creating event.
     *
     * @param  Reconciliation  $model
     * @return void
     */
    public function creating(Reconciliation $model)
    {
        # add creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # set the initial status
        $model->setAttribute($model->getStatusFieldName(), $model->getAttribute($model->getStatusFieldName()) ?: $this->getInitialStatus());
    }

    /**
     * Listen to the model's creating event.
     *
     * @param  Reconciliation $model
     * @return void
     */
    public function saved(Reconciliation $model)
    {
        # Record Histories
        $this->recordHistories($model);
        # Fire reconciled event
        if($model->isDirty('reconciliation_status_id')){
            $this->fireStatusEvents($model);
            # checks if reconcilation is in FINAL_RECONCILIATION_COMPLETE status, then update program to reconciled
            $this->onFinalReconciliationComplete($model);
        }
    }

    /**
     * Return the Initial Status
     *
     * @return int
     */
    protected function getInitialStatus()
    {
        return object_get($this, 'initial_status', Status::CLOSEOUT_INITIATED);
    }

    /**
     * Fire events
     *
     * @param  Reconciliation $model
     * @return Void
     */
    protected function fireStatusEvents(Reconciliation $model)
    {
        # we have next status
        $next = $model->getAttribute($model->getStatusFieldName());
        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }

    /**
     * checks if reconcilation is in FINAL_RECONCILIATION_COMPLETE status, then update program to reconciled
     * @param  Reconciliation $model
     * @return void
     */
    private function onFinalReconciliationComplete(Reconciliation $model)
    {
        if($model->is_final_recon_complete){
            $model->program->update(['is_reconciled'=>true]);
        }
    }

    /**
     * Record History Progression for the Reconciliation
     *
     * @param  Reconciliation $model
     * @return Void
     */
    protected function recordHistories(Reconciliation $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($model->getStatusFieldName());
        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());
        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));
    }
}
