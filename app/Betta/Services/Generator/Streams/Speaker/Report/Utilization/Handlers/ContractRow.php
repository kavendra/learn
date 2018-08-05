<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Utilization\Handlers;

use Betta\Models\Contract;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ContractRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Customer Master Id',
        'Last Name',
        'First Name',
        'NPI',
        'Contract Status',
        'Brand',
        'Contract Start Date',
        'Contract End Date',
        'Total Completed/Closed Out',
        'Hono Completed/Closed Out',
        'Total Upcoming/Confirmed',
        'Hono Upcoming/Confirmed',
        'Hono Cancelled/Paid',
        'Total Programs',
        'Total Hono',
        'Threshold',
        'Max Cap',
        'Representative Name',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
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
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Get Customer Master ID
     *
     * @return string
     */
    protected function getCustomerMasterIdAttribute()
    {
        return data_get($this->contract, 'profile.customer_master_id', '');
    }

    /**
     * Last Name of the Profile
     *
     * @return string
     */
    protected function getLastNameAttribute()
    {
        return data_get($this->contract, 'profile.last_name', '');
    }

    /**
     * First Name of the Profile
     *
     * @return string
     */
    protected function getFirstNameAttribute()
    {
        return data_get($this->contract, 'profile.first_name', '');
    }

    /**
     * Contract Status Label
     *
     * @return string
     */
    protected function getContractStatusAttribute()
    {
        return $this->contract->status_label;
    }

    /**
     * Brand of the Contract
     *
     * @return string
     */
    protected function getBrandAttribute()
    {
        return data_get($this->contract, 'brand.label');
    }

    /**
     * Contract Status Start Date
     *
     * @return float
     */
    protected function getContractStartDateAttribute()
    {
        return excel_date($this->contract->valid_from);
    }

    /**
     * Contract Status End Date
     *
     * @return float
     */
    protected function getContractEndDateAttribute()
    {
        return excel_date($this->contract->valid_to);
    }

    /**
     * Contract Profile NPI
     *
     * @return string
     */
    protected function getNpiAttribute()
    {
        return data_get($this->contract, 'profile.hcpProfile.npi', '');
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
     * Resolve the value of
     *
     * @return string
     */
    protected function getThresholdAttribute()
    {
        return $this->contract->maxcaps->implode('threshold',' | ');
    }

    /**
     * Resolve the value of
     *
     * @return string
     */
    protected function getMaxCapAttribute()
    {
        return $this->contract->maxcaps->implode('honorarium_limit',' | ');
    }

    /**
     * Resolve the value of
     *
     * @return string
     */
    protected function getRepresentativeNameAttribute()
    {
        return data_get($this->contract->nominations->last(), 'owner.preferred_name');
    }

    /**
     * Contract ID
     *
     * @return numeric
     */
    protected function getIdAttribute()
    {
        return $this->contract->getKey();
    }

    /**
     * Base list of the Programs we need to work with:
     *
     * @return Collection (of ProgramSpeakers)
     */
    protected function getRelatedAttribute()
    {
        return $this->contract->confirmed_or_cancelled_speaks
                    ->where('program.programType.is_speaker_program', true)
                    ->keyBy('id');
    }

    /**
     * StoreCollect Confirmed or Cancelled Speaks
     *
     * @return Collection
     */
    protected function getConfirmedAttribute()
    {
        return $this->related->where('is_confirmed', true);
    }

    /**
     * StoreCollect Confirmed or Cancelled Speaks
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
}
