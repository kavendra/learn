<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\AnnualUtilization\Handlers;

use Betta\Models\Nomination;
use Betta\Models\ProgramSpeaker;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ActiveNominationsRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Nomination
     */
    protected $nomination;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Customer Master ID',
        'Last Name',
        'First Name',
        'Contract Expiration Date',
        'NPI',
        'Total Completed/Closed Out',
        'Hono Completed/Closed Out',
        'Total Upcoming/Confirmed',
        'Hono Upcoming/Confirmed',
        'Hono Cancelled Paid',
        'Total # Programs',
        'Total Hono',
        'Last Complete',
        'Territory ID',
        'Territory Name',
        'Representative Name',
        'District ID',
        'District Name',
        'District Manager',
        'Region ID',
        'Region Name',
        'National Sales Director',
        'Max Hono',
        'Tier',
        'Pending Requirements',
        'Speaker Bureau',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile',
        'owner',
        'district_owner',
        'region_owner',
        'related',
        'confirmed',
        'completed',
        'upcoming',
        'cancelled',
        'sum_completed',
        'sum_upcoming',
        'sum_cancelled',
        'max_hono',
        'total_hono',
        'sum_cancelled',
        'latest_contract',
        'trainings',
        'complianceTrainings',
    ];

    /**
     * Create new Row instance
     *
     * @param Illuminate\Support\Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->nomination = $collection->first();
    }

    /**
     * Get Customer Master ID
     *
     * @return string
     */
    protected function getCustomerMasterIdAttribute()
    {
        return $this->profile->customer_master_id;
    }

     /**
     * Last Name of the Profile
     *
     * @return string
     */
    protected function getLastNameAttribute()
    {
        return $this->profile->last_name;
    }

    /**
     * First Name of the Profile
     *
     * @return string
     */
    protected function getFirstNameAttribute()
    {
        return $this->profile->first_name;
    }

    /**
     * Profile NPI
     *
     * @return string
     */
    protected function getNpiAttribute()
    {
        return data_get($this->profile, 'hcpProfile.npi') ?: '';
    }

    /**
     * Resolve the value of the total Completed and Closed Out
     *
     * @return numeric
     */
    protected function getTotalCompletedClosedOutAttribute()
    {
        return $this->completed->count();
    }

    /**
     * Resolve the value of
     *
     * @return numeric
     */
    protected function getHonoCompletedClosedOutAttribute()
    {
        return $this->sum_completed;
    }

    /**
     * Resolve the value of
     *
     * @return numeric
     */
    protected function getTotalUpcomingConfirmedAttribute()
    {
        return $this->upcoming->count();
    }

    /**
     * Resolve the value of
     *
     * @return numeric
     */
    protected function getHonoUpcomingConfirmedAttribute()
    {
        return $this->sum_upcoming;
    }

    /**
     * Resolve the value of
     *
     * @return numeric
     */
    protected function getHonoCancelledPaidAttribute()
    {
        return $this->sum_cancelled;
    }

    /**
     * Resolve the value of
     *
     * @return numeric
     */
    protected function getTotalProgramsAttribute()
    {
        return $this->confirmed->count();
    }

    /**
     * Total Calculated honoraria
     *
     * @return float
     */
    protected function getTotalHonoAttribute()
    {
        return array_sum([
            $this->sum_completed,
            $this->sum_upcoming,
            $this->sum_cancelled,
        ]);
    }

    /**
     * Base list of the Programs we need to work with:
     *
     * @return Collection (of ProgramSpeakers)
     */
    protected function getRelatedAttribute()
    {
        return $this->collection->pluck('profile.speaks')->collapse()->unique()
                    ->where('program.programType.is_speaker_program', true)
                    ->keyBy('id');
    }

    /**
     * Collect All items where the Speaker is Confirmed
     *
     * @return Collection
     */
    protected function getConfirmedAttribute()
    {
        return $this->related->where('is_confirmed', true);
    }

    /**
     * Collect Speaks where Program is COmpleted / Close or Closeout out
     *
     * @return Collection
     */
    protected function getCompletedAttribute()
    {
        return $this->confirmed->filter(function($programSpeaker){
            return $programSpeaker->program->is_completed
                or $programSpeaker->program->is_closed
                or $programSpeaker->program->is_closed_out;
        });
    }

    /**
     * Diff Confirmed against Completed
     *
     * @return Collection
     */
    protected function getUpcomingAttribute()
    {
        return $this->confirmed->diffKeys($this->completed);
    }

    /**
     * Collect cancelled records
     *
     * @return Collection
     */
    protected function getCancelledAttribute()
    {
        return $this->related->where('is_cancelled', true);
    }

    /**
     * Sum the completed Honoraria
     *
     * @return float
     */
    protected function getSumCompletedAttribute()
    {
        return $this->completed->pluck('costs')->collapse()->where('is_honoraria', true)->sum('calculated');
    }

    /**
     * Sum the Upcoming programs
     *
     * @return float
     */
    protected function getSumUpcomingAttribute()
    {
        return $this->upcoming->pluck('costs')->collapse()->where('is_honoraria', true)->sum('calculated');
    }

    /**
     * Sum every cancelled program
     *
     * @return float
     */
    protected function getSumCancelledAttribute()
    {
        return $this->cancelled->pluck('costs')->collapse()->where('is_honoraria', true)->sum('calculated');
    }

    /**
     * Last Complete Engagement
     *
     * @return float
     */
    protected function getLastCompleteAttribute()
    {
        return excel_date($this->completed->max('program.start_date'));
    }

    /**
     * Territory of the Owner
     *
     * @return strin
     */
    protected function getTerritoryIdAttribute()
    {
        return data_get($this->owner, 'territory.account_territory_id');
    }

    /**
     *
     *
     * @return strin
     */
    protected function getTerritoryNameAttribute()
    {
        return data_get($this->owner, 'territory.label');
    }

    /**
     * Owner of the last Nomination
     *
     * @return strin
     */
    protected function getOwnerAttribute()
    {
        return $this->nomination->owner;
    }

    /**
     * Name of the last Nomination' Owner
     *
     * @return string
     */
    protected function getRepresentativeNameAttribute()
    {
        return data_get($this->owner, 'preferred_name');
    }

    /**
     * ID for the Owner's Manager District
     *
     * @return string
     */
    protected function getDistrictIdAttribute()
    {
        return data_get($this->district_owner, 'territory.account_territory_id');
    }

    /**
     * Label for the Owner's Manager District
     *
     * @return string
     */
    protected function getDistrictNameAttribute()
    {
        return data_get($this->district_owner, 'territory.label');
    }

    /**
     * ID for the Owner's Manager Parent District
     *
     * @return string
     */
    protected function getRegionIdAttribute()
    {
        return data_get($this->region_owner, 'territory.label');
    }

    /**
     * ID for the Owner's Manager Parent District
     *
     * @return string
     */
    protected function getRegionNameAttribute()
    {
        return data_get($this->region_owner, 'territory.account_territory_id');
    }

    /**
     * Return the Parent of the Owner
     *
     * @return Profile | null
     */
    protected function getDistrictOwnerAttribute()
    {
        return data_get($this->owner, 'parent');
    }

    /**
     * Return the Parent of the Owner
     *
     * @return Profile | null
     */
    protected function getRegionOwnerAttribute()
    {
        return data_get($this->district_owner, 'parent');
    }


    /**
     * Name of the last Nomination's Owner' Parent
     *
     * @return string
     */
    protected function getDistrictManagerAttribute()
    {
        return data_get($this->district_owner, 'preferred_name');
    }

    /**
     * Name of the last Nomination's Owner' GrandParent
     *
     * @return strin
     */
    protected function getNationalSalesDirectorAttribute()
    {
        return data_get($this->region_owner, 'preferred_name');
    }


    /**
     * Resolve Profile from the collection of ProgramSpeakers
     *
     * @return Profile
     */
    protected function getProfileAttribute()
    {
        return $this->nomination->profile;
    }

    /**
     * Get Nomination's Latest Contract
     *
     * @access hidden latest_contract
     * @return Contract | null
     */
    public function getLatestContractAttribute()
    {
        return $this->nomination->latest_contract;
    }

    /**
     * Get the Max Hono of the Speaker
     *
     * @return decimal
     */
    protected function getMaxHonoAttribute()
    {
        if($contract = $this->latest_contract){
            return $contract->maxCaps->sum('honorarium_limit');
        }

        return null;
    }

    /**
     * Get the Max Hono of the Speaker
     *
     * @return decimal
     */
    protected function getTierAttribute()
    {
        return $this->nomination->tier_label;
    }

    /**
     * Display the Pending Requirements Attribute
     *
     * @return string
     */
    protected function getPendingRequirementsAttribute()
    {
        $requirements = [
            $this->hasMissingCompliance(),
            $this->hasMissingProduct(),
            $this->hasMissingAttestations(),
            $this->hasMissingTravelCode(),
        ];

        return implode(', ', array_filter($requirements));
    }

    /**
     * List  all the trainings for the speaker
     *
     * @return Illuminate\Support\Collection
     */
    protected function getTrainingsAttribute()
    {
        return $this->profile->trainings->filter(function($training){
            return $training->hasBrand($this->nomination->brand_id);
        });
    }

    /**
     * List  all the trainings for the speaker
     *
     * @access hidden
     * @return Illuminate\Support\Collection (of TrainingCourses)
     */
    protected function getComplianceTrainingsAttribute()
    {
        return $this->nomination->brand->trainingCourses->where('is_compliance', true)->keyBy('id');
    }

    /**
     * Resolve if any of the  compliance training is missing
     *
     * @return string
     */
    protected function hasMissingCompliance()
    {
        return $this->complianceTrainings->diff( $this->existingComplianceTrainigs() )->isNotEmpty()
             ? 'Compliance'
             : null;

    }

    /**
     * Resolve trainings courses from all completed trainings that are compliance
     *
     * @return Illuminate\Support\Collection (of Trainings Courses )
     */
    protected function existingComplianceTrainigs()
    {
        return $this->trainings->where('is_compliance', true)->pluck('trainingCourse')->keyBy('id');
    }

    /**
     * Resolve if any of the missing production training is present
     *
     * @return string
     */
    protected function hasMissingProduct()
    {
        $product = $this->trainings->where('is_product', true);

        return ($product->isEmpty() or $product->where('is_completed', false)->isNotEmpty())
             ? 'Product'
             : null;
    }

    /**
     * Resolve if any of the missing attestations that are not Travel Conduct
     *
     * @return string
     */
    protected function hasMissingAttestations()
    {
        return $this->profile->attestations->where('pivot.attestation_id', '=', 1)->isEmpty()
             ? 'Code of Conduct'
             : null;
    }

    /**
     * Resolve if any of the missing attestations that are Travel Conduct
     *
     * @return string
     */
    protected function hasMissingTravelCode()
    {
        return $this->profile->attestations->where('pivot.attestation_id', '=', 2)->isEmpty()
             ? 'Travel Code'
             : null;
    }

    /**
     * Get Speaker Bureau
     *
     * @return void
     */
    public function getSpeakerBureauAttribute()
    {
        return $this->profile->speakerBureaus->filter(function($bureau){
            return $bureau->brand->is($this->nomination->brand)
               and $this->nomination->validIntersect($bureau->pivot->valid_from, $bureau->pivot->valid_to);
        })->implode('label',  ', ');
    }

    /**
     * Max Date between all contracts
     *
     * @return string
     */
    public function getContractExpirationDateAttribute()
    {
        if($contract = $this->latest_contract){
            return excel_date($contract->valid_to);
        }
        # nothing
        return '';
    }
}
