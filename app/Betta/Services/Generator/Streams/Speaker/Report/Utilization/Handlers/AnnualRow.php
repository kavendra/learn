<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Utilization\Handlers;

use Betta\Models\Nomination;
use Betta\Models\ProgramSpeaker;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class AnnualRow extends AbstratRowHandler
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
}
