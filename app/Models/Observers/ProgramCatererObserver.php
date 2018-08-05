<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramCaterer;
use Betta\Models\ProgressionStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramCatererObserver extends AbstractObserver
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
    protected $statusEvents = [
        ProgressionStatus::REQUESTED => 'App\Events\Program\Caterer\Requested',
        ProgressionStatus::INITIATED => 'App\Events\Program\Caterer\Initiated',
        ProgressionStatus::DECLINED => 'App\Events\Program\Caterer\Declined',
        ProgressionStatus::ACCEPTED => 'App\Events\Program\Caterer\Accepted',
        ProgressionStatus::CONFIRMED => 'App\Events\Program\Caterer\Confirmed',
        ProgressionStatus::NOT_QUALIFIED => 'App\Events\Program\Caterer\NotQualified',
        ProgressionStatus::QUALIFIED => 'App\Events\Program\Caterer\Qualified',
        ProgressionStatus::CANCELLED => 'App\Events\Program\Caterer\Cancelled',
        ProgressionStatus::NOT_NEEDED => 'App\Events\Program\Caterer\NotNeeded',
        ProgressionStatus::NOT_REQUIRED => 'App\Events\Program\Caterer\NotRequired',
    ];


    /**
     * Listen to the ProgramCaterer creating event.
     *
     * @param  ProgramCaterer  $model
     * @return void
     */
    public function creating(ProgramCaterer $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # set Initial Status
        $model->setAttribute('progression_status_id', $model->getAttribute('progression_status_id') ?: $this->initialState );

        # Set the Primary flag accordingly
        $this->setPrimaryFlag( $model, $model->program->programCaterers()->get()->where('is_primary', true)->isEmpty() );
    }

    /**
     * Listen to the ProgramCaterer created event.
     *
     * @param  ProgramCaterer  $model
     * @return void
     */
    public function created(ProgramCaterer $model)
    {
        $model->progressions()->create(['to_status_id' => $model->progressionStatus->id]);
    }


    /**
     * Listen to the ProgramCaterer saved event.
     *
     * @param  ProgramCaterer  $model
     * @return void
     */
    public function saved(ProgramCaterer $model)
    {
        if($model->isDirty('progression_status_id')){
            $this->fireStatusEvents($model);
        }

        # update primary of all the other programCaterers to false before
        $remaining = $model->program->programCaterers()->where('id','<>',$model->id)->get();

        if($model->is_primary){
            foreach($remaining as $remaining){
                $otherCaterers = ProgramCaterer::find($remaining->id);
                $otherCaterers->is_primary = 0;
                $otherCaterers->save();
            }
        }

    }


    /**
     * When we remove the Location and have to reset the primary
     *
     * @param  ProgramCaterer $model
     * @return
     */
    public function deleted(ProgramCaterer $model)
    {
        # Load all non-deleted caterers
        $remaining = $model->program->programCaterers()->get();

        # if model was primary
        if ($remaining->where('is_primary', true)->isEmpty() ){
            # Make the first Primary
            if($first = $remaining->first()){
                # Careful, as we just triggered a series of events
                $this->setPrimaryFlag($first, true)->save();
            }
        }
    }


    /**
     * If there are no other locations, set this as primary
     *
     * @param ProgramCaterer $model
     * @return ProgramCaterer $model
     */
    protected function setPrimaryFlag(ProgramCaterer $model, $value)
    {
        # Set value
        $model->setAttribute('is_primary', $value );

        # chain for return
        return $model;
    }


    /**
     * Fire events
     *
     * @param  Program $model
     * @return Void
     */
    protected function fireStatusEvents(ProgramCaterer $model)
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
