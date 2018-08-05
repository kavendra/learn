<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramSpeaker;
use Betta\Models\ProgressionStatus;
use App\Events\Program\Speaker\MadePrimary;
use App\Events\Program\Speaker\MadeNonPrimary;
use App\Events\Program\Speaker\SyncHonorarium;
use Betta\Foundation\Eloquent\AbstractObserver;
use Betta\Models\Interfaces\ProgramSpeakerTypesInterface;

class ProgramSpeakerObserver extends AbstractObserver implements ProgramSpeakerTypesInterface
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = ProgressionStatus::REQUESTED;

    /**
     * Create the list of events
     *
     * @var Array
     */
    protected $statusEvents = [
        ProgressionStatus::REQUESTED => 'App\Events\Program\Speaker\Requested',
        ProgressionStatus::INITIATED => 'App\Events\Program\Speaker\Initiated',
        ProgressionStatus::DECLINED => 'App\Events\Program\Speaker\Declined',
        ProgressionStatus::ACCEPTED => 'App\Events\Program\Speaker\Accepted',
        ProgressionStatus::CONFIRMED => 'App\Events\Program\Speaker\Confirmed',
        ProgressionStatus::NOT_QUALIFIED => 'App\Events\Program\Speaker\NotQualified',
        ProgressionStatus::QUALIFIED => 'App\Events\Program\Speaker\Qualified',
        ProgressionStatus::CANCELLED => 'App\Events\Program\Speaker\Cancelled',
        ProgressionStatus::NOT_NEEDED => 'App\Events\Program\Speaker\NotNeeded',
        ProgressionStatus::NOT_REQUIRED => 'App\Events\Program\Speaker\NotRequired',
    ];

    /**
     * Set the Status Field
     *
     * @var string
     */
    protected $statusField = 'progression_status_id';

    /**
     * Listen to the ProgramSpeaker creating event.
     *
     * @param  ProgramSpeaker  $model
     * @return void
     */
    public function creating(ProgramSpeaker $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # set Initial Status
        $model->setAttribute('progression_status_id', $model->getAttribute('progression_status_id') ?: $this->initialState );
        # check if there are any other speaker and if so, current is not primary
        $this->setPrimaryFlag($model);
    }

    /**
     * Whenever we are saving the speaker, let's recalculate the distance
     *
     * @param  ProgramSpeaker $model
     * @return void
     */
    public function saving(ProgramSpeaker $model)
    {
        # Udpate the Driving Distance
        $model->setAttribute('driving_distance', $model->distance_to_location);
    }

    /**
     * Listen to the ProgramSpeaker saved event.
     *
     * @param  ProgramSpeaker  $model
     * @return void
     */
    public function saved(ProgramSpeaker $model)
    {
        if ($model->isDirty('progression_status_id')){
            # Record History
            $this->addHistory($model);
            # Fire the events
            $this->fireStatusEvents($model);
            # Touch other speakers
            $this->touchAdjacentSpeaks($model);
        }

        # if the ProgramSpeaker is Primary, lets fire the job to add the Honorarium
        # If the ProgramSpeaker is Not Primary, let's remove Costs
        if ($model->is_primary){
            event(new MadePrimary($model));
            # update primary of all the other programSpeakers to false before
            $remaining = $model->program->programSpeakers()->where('id','<>',$model->id)->get();
            # we are getting them fresh so not to re-trigger the same event in infinite loop
            foreach($remaining as $remaining){
                $otherSpeakers = ProgramSpeaker::find($remaining->id);
                $otherSpeakers->is_primary = 0;
                $otherSpeakers->save();
            }
        } else {
            event(new MadeNonPrimary($model));
        }
    }


    /**
     * Listen to the ProgramSpeaker deleted event.
     *
     * @param  ProgramSpeaker  $model
     * @return void
     */
    public function deleted(ProgramSpeaker $model)
    {
        # Delete honoraria costs
        $model->honoraria()->delete();
        # Delete Travel Costs
        $model->travelCosts()->delete();
        # Removing nomination -> but only for the nominating programs
        if( object_get($model->program, 'speaker_type') == static::NEW_NOMINATION ){
            $this->cleanUpNomination($model);
        }
        # when the last speaker is deleted, we shall reset the program_speaker_type to null
        if($model->program->programSpeakers->isEmpty()){
            $model->program->update(['speaker_type'=>null]);
        }
        # When only one speaker left, they must be Primary
        if($model->program->programSpeakers()->count() == 1){
            # get the first
            $first = $model->program->programSpeakers()->first();
            # Set Primary to first
            $this->setPrimaryFlag( $first )->save();
        }
    }

    /**
     * If there are no other locations, set this as primary
     *
     * @param   ProgramSpeaker $model
     * @return  ProgramSpeaker $model
     */
    protected function setPrimaryFlag(ProgramSpeaker $model)
    {
        # get all Primary Speakers, see if there are any
        $primaryFlag = $model->program->programSpeakers->where('is_primary', true)->isEmpty();
        # Set Primary Flag
        $model->setAttribute('is_primary', $model->getAttribute('is_primary') ?: $primaryFlag );
        # Return
        return $model;
    }

    /**
     * Clean up nomination details
     *
     * @param  ProgramSpeaker $model
     * @return Void
     */
    protected function cleanUpNomination(ProgramSpeaker $model)
    {
        # We have checked if the program type is new nomination,
        # So we now need to see if:
        # Speaker is primary AND Nomination is initiated
        if ($model->is_primary AND object_get($model->nomination, 'is_initiated') ){
            # Remove Nomination, SoftDelete
            $model->nomination->delete();
            # Add notes to Profile
            $model->profile->notes()->create(['content'=>"Removed {$model->profile->preferred_name} nomination in program {$model->program_id} planning."]);
            # Remove Speaker Profile, SoftDelete
            if($speakerProfile = object_get($model->profile,'speakerProfile')){
                # Remove Speaker Profile
                $speakerProfile->delete();
                # Leave notes
                $model->profile->notes()->create(['content'=>"Removed {$model->profile->preferred_name} speaker profile in program {$model->program_id} planning."]);
            }
            # If there are more speakers, change the p.speaker_type
            if ( $model->program->programSpeakers()->count() ){
                # Update the Upper program Scope
                $model->program->update(['speaker_type' => static::SPEAKER_BUREAU]);
            }
        }
    }

    /**
     * Fire events
     *
     * @param  ProgramSpeaker $model
     * @return Void
     */
    protected function fireStatusEvents(ProgramSpeaker $model)
    {
        # we have next status
        $next = $model->getAttribute($this->statusField);
        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }

    /**
     * Record history
     *
     * @param  ProgramSpeaker $model
     * @return Void
     */
    protected function addHistory(ProgramSpeaker $model)
    {
        # We have Previous status
        $from_status_id = $model->getOriginal('progression_status_id');
        # We have next status
        $to_status_id = $model->getAttribute('progression_status_id');
        # Progress
        $model->progressions()->create(compact('from_status_id', 'to_status_id'));
    }

    /**
     * Touch adjacent Speaks for the same speaker
     *
     * @param  ProgramSpeaker $model
     * @return void
     */
    protected function touchAdjacentSpeaks(ProgramSpeaker $model)
    {
        # Select all speaks for
        # - the same speaker
        # - on the same day,
        # - excluding current,
        # - have costs
        # and for each of those -> trigger SyncHonorarium
        $this->adjacentSpeaks($model)->each(function($programSpeaker){
            event(new SyncHonorarium($programSpeaker));
        });
    }

    /**
     * Collect all Speaks current speaker has on the same day, excluding current
     *
     * @param  ProgramSpeaker $model
     * @return Illuminate\Support\collection
     */
    protected function adjacentSpeaks(ProgramSpeaker $model)
    {
        return $model->whereProfileId($model->profile_id)
                     ->notInStatus([
                        ProgressionStatus::DECLINED,
                        ProgressionStatus::NOT_QUALIFIED,
                        ProgressionStatus::CANCELLED,
                        ProgressionStatus::NOT_NEEDED,
                        ProgressionStatus::NOT_REQUIRED,
                     ])
                     ->whereHas('program', function($program) use ($model){
                        $program->at($model->program->start_date)
                                ->excludeKey([$model->program_id])
                                ->speakerPrograms();
                     })
                     ->has('honoraria')
                     ->get();
    }
}
