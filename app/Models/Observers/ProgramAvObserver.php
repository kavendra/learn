<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramAv;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramAvObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = 1;

    /**
     * List events to fire on status change
     *
     * @var array
     */
    protected $statusEvents = [];
    

    /**
     * Listen to the ProgramAv creating event.
     *
     * @param  ProgramAv  $model
     * @return void
     */
    public function creating(ProgramAv $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        $model->setAttribute('progression_status_id', $model->getAttribute('progression_status_id') ?: $this->initialState );
    }

    /**
     * Listen to the ProgramAv created event.
     *
     * @param  ProgramAv  $model
     * @return void
     */
    public function created(ProgramAv $model)
    {
        $model->progressions()->create(['to_status_id' => $model->progressionStatus->id]);
    }

    /**
     * Listen to the ProgramAV saved event.
     *
     * @param  ProgramAV  $model
     * @return void
     */
    public function saved(ProgramAV $model)
    {
        
        if($model->isDirty('progression_status_id')){
            $this->fireStatusEvents($model);
        }

    }

    /**
     * Fire events
     *
     * @param  Program $model
     * @return Void
     */
    protected function fireStatusEvents(ProgramAv $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal('progression_status_id');

        # we have next status
        $to_status_id = $model->getAttribute('progression_status_id');

        # Inject history
        $model->progressions()->create(compact('from_status_id','to_status_id'));

        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $to_status_id)){
            event (new $event($model));
        }
    }


}
