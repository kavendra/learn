<?php

namespace Betta\Models\Observers;

use Betta\Models\Vendor;
use Betta\Models\ProgramLocation;
use Betta\Models\ProgressionStatus;
use App\Events\ProgramSpeaker\MadePrimary;
use Betta\Foundation\Eloquent\AbstractObserver;
use App\Events\Program\Location\UpdatedContactInformation;

class ProgramLocationObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = ProgressionStatus::REQUESTED;

    /**
     * List events to fire on status change
     *
     * @var array
     */
    protected $statusEvents = [
        ProgressionStatus::REQUESTED => 'App\Events\Program\Location\Requested',
        ProgressionStatus::INITIATED => 'App\Events\Program\Location\Initiated',
        ProgressionStatus::DECLINED => 'App\Events\Program\Location\Declined',
        ProgressionStatus::ACCEPTED => 'App\Events\Program\Location\Accepted',
        ProgressionStatus::CONFIRMED => 'App\Events\Program\Location\Confirmed',
        ProgressionStatus::NOT_QUALIFIED => 'App\Events\Program\Location\NotQualified',
        ProgressionStatus::QUALIFIED => 'App\Events\Program\Location\Qualified',
        ProgressionStatus::CANCELLED => 'App\Events\Program\Location\Cancelled',
        ProgressionStatus::NOT_NEEDED => 'App\Events\Program\Location\NotNeeded',
        ProgressionStatus::NOT_REQUIRED => 'App\Events\Program\Location\NotRequired',
    ];

    /**
     * Listen to the ProgramLocation creating event.
     *
     * @param  ProgramLocation  $model
     * @return void
     */
    public function creating(ProgramLocation $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # set Initial Status
        $model->setAttribute('progression_status_id', $model->getAttribute('progression_status_id') ?: $this->initialState );
        # check if there are any other addresses in owner and if so, current is not primary
        $this->setPrimaryFlag($model, $model->program->programLocations()->get()->isEmpty() );
        # Set the FB Max per person
        $this->setFbMax($model);
        # Replicate Contact information into Model
        $this->populateContactInformation($model);
    }

    /**
     * Listen to the ProgramLocation created event.
     *
     * @param  ProgramLocation  $model
     * @return void
     */
    public function created(ProgramLocation $model)
    {
        $model->progressions()->create(['to_status_id' => $model->progressionStatus->id]);
        # Once we created the Program Location, we can update theprogram on-offsite flag
        $model->program->update( $model->only('address.is_onsite') );
    }

    /**
     * Listen to the ProgramLocation saving event.
     *
     * @param  ProgramLocation  $model
     * @return void
     */
    public function saving(ProgramLocation $model)
    {
        # Set Nulalble values to null if empty
        $this->setNullableFields($model);
    }

    /**
     * Listen to the ProgramLocation saved event.
     *
     * @param  ProgramLocation $model
     * @return Void
     */
    public function saved(ProgramLocation $model)
    {
        # If Location is set Primary, we need to recalculate all estimates and distances
        if($model->is_primary){
            # re-refresh the relation
            # ProgramSpeaker will update the distance
            $model->program->programSpeakers->each(function($speaker) use($model){
                # Udpate the Driving Distance
                # Updating the speaker will also trigger the reclculation of honoraria and travel costs
                if($speaker->address AND $model->address){
                    $speaker->update(['driving_distance'=> $speaker->address->distanceTo($model->address)]);
                }
            });
            # update primary of all the other programLocations to false before
            $remaining = $model->program->programLocations()->where('id','<>',$model->id)->get();
            # Update the rest
            foreach($remaining as $remaining){
                $otherLocations = ProgramLocation::find($remaining->id);
                $otherLocations->is_primary = 0;
                $otherLocations->save();
            }
        }
        # Fire the progression events
        if($model->isDirty('progression_status_id')){
            $this->fireStatusEvents($model);
        }
    }

    /**
     * When we remove the Location and have to reset the primary
     *
     * @param  ProgramLocation $model
     * @return
     */
    public function deleted(ProgramLocation $model)
    {
        # Load all non-deleted locations
        $remaining = $model->program->programLocations()->get();
        # if model was primary
        if ($remaining->where('is_primary', true)->isEmpty() ){
            # Make the first Primary
            if($first = $remaining->first()){
                # Careful, as we just triggered a seried of events
                $this->setPrimaryFlag($first, true)->save();
            }
        }
        # reset all driving distances
        if($remaining->isEmpty()){
            $model->program->programSpeakers->each(function($speaker){
                # Udpate the Driving Distance
                # Updating the speaker will also trigger the reclculation of honoraria and travel costs
                $speaker->update(['driving_distance'=> 0]);
           });
        }
    }

    /**
     * Listen to ProgramLocation update event
     *
     * @param  ProgramLocation $model
     * @return void
     */
    public function updated(ProgramLocation $model)
    {
        $contactInformation = [
            'contact_name',
            'contact_email',
            'contact_phone',
        ];

        if($model->isDirty($contactInformation)){
            event (new UpdatedContactInformation($model));
        }
    }

    /**
     * Replicate information into the Model
     *
     * @param  ProgramLocation $model
     * @return
     */
    protected function populateContactInformation(ProgramLocation $model)
    {
        if($vendor = data_get($model, 'address.owner') AND $vendor instanceOf Vendor){
            # I imagine we are working on the same model.. right?
            $model->fill([
                'contact_name'  => $vendor->contact_name ?: '' ,
                'contact_email' => $vendor->contact_email ?: '' ,
                'contact_phone' => $vendor->contact_phone ?: '' ,
            ]);
        }
    }

    /**
     * Set all ampty fields to null
     *
     * @param ProgramLocation $model
     */
    protected function setNullableFields(ProgramLocation $model)
    {
        #all these are nullable
        $nullables = [
            'onsite_av_cost',
            'menu_alacart_price',
            'menu_preselected_price',
            'deposit',
            'final_guarantee_date',
            'final_guarantee_number',
            'final_guarantee_count',
        ];
        # for each nullable field, reset values
        foreach($nullables as $nullable){
            $this->setNullableField($model, $nullable);
        }
    }

    /**
     * If there are no other locations, set this as primary
     *
     * @param ProgramLocation $model
     * @return ProgramLocation
     */
    protected function setPrimaryFlag(ProgramLocation $model, $value)
    {
        # set Primary Flag
        $model->setAttribute('is_primary', $value );
        # return Model
        return $model;
    }

    /**
     * Set the FB Max per person within local
     *
     * @param  ProgramLocation $model
     * @return ProgramLocation
     */
    protected function setFbMax(ProgramLocation $model)
    {
        # get the value from Program
        $value = $model->program->estimateFbCostPerPerson('max');
        # set FB Max per person
        $model->setAttribute('fb_max_per_person', $value);
        # return Model
        return $model;
    }

    /**
     * Fire events
     *
     * @param  Program $model
     * @return Void
     */
    protected function fireStatusEvents(ProgramLocation $model)
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
