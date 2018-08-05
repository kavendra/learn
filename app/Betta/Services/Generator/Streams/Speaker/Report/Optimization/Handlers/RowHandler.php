<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Optimization\Handlers;

use Betta\Models\Nomination;
use Betta\Foundation\Helpers\DateFormats;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Nomination
     */
    public $nomination;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Speaker Brand',
        'ACTIVE Speaker Name',
        'Speaker City',
        'Speaker State',
        'Speaker Tier',
        'Representative',
        'District/Area',
        'Regional',
        'Contract Start Date',
        'Contract End Date',
        'Number of Completed Programs',
        'Number of Pending Programs',
        'Total Programs',
        'Approved/Not Approved',
        'Medical Affairs',
        'Medical Affairs Confirmed Tier',
        'Compliance',
        'Brand',
        'Speaker Bureau',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile',
        'address',
        'owner',
        'parent',
        'region',
        'contract',
        'related',
        'confirmed',
        'completed',
        'upcoming',
        'cancelled',
    ];

    /**
     * Create new Row instance
     *
     * @param Nomination $nomination
     */
    public function __construct(Nomination $nomination)
    {
        $this->nomination = $nomination;
    }

    /**
     * Resolve profile
     *
     * @access hidden
     * @return string
     */
    protected function getProfileAttribute()
    {
        return $this->nomination->profile;
    }

    /**
     * Resolve Address
     *
     * @access hidden
     * @return string
     */
    protected function getAddressAttribute()
    {
        return data_get($this->profile, 'preferred_address');
    }

    /**
     * Resolve Owner
     *
     * @access hidden
     * @return string
     */
    protected function getOwnerAttribute()
    {
        return $this->nomination->owner;
    }

    /**
     * Resolve Owner's Parent
     *
     * @access hidden
     * @return string
     */
    protected function getParentAttribute()
    {
        return data_get($this->owner, 'parent');
    }

    /**
     * Resolve Owner's Parent
     *
     * @access hidden
     * @return string
     */
    protected function getRegionAttribute()
    {
        return data_get($this->parent, 'parent');
    }

    /**
     * Resolve Nominations' latest contract
     *
     * @access hidden
     * @return string
     */
    protected function getContractAttribute()
    {
        return $this->nomination->latest_contract;
    }

    /**
     * Base list of the Programs we need to work with:
     *
     * @access hidden
     * @return Collection (of ProgramSpeakers)
     */
    protected function getRelatedAttribute()
    {
        # get the speaks
        $speaks = data_get($this->profile, 'speaks') ?: collect([]);
        # all the SPeaker programs, with matching brand, within matching period
        return $speaks->where('program.programType.is_speaker_program', true)
                      ->where('program.primaryBrand.id', $this->nomination->brand_id)
                      ->filter(function($speak){
                        return $this->nomination->validAt( $speak->program->start_date );
                      })
                      ->keyBy('id');
    }

    /**
     * Collect All items where the Speaker is Confirmed
     *
     * @access hidden
     * @return Collection
     */
    protected function getConfirmedAttribute()
    {
        return $this->related->where('is_confirmed', true);
    }

    /**
     * Collect Speaks where Program is COmpleted / Close or Closeout out
     *
     * @access hidden
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
     * @access hidden
     * @return Collection
     */
    protected function getUpcomingAttribute()
    {
        return $this->confirmed->diffKeys($this->completed);
    }

    /**
     * Collect cancelled records
     *
     * @access hidden
     * @return Collection
     */
    protected function getCancelledAttribute()
    {
        return $this->related->where('is_cancelled', true);
    }

    /**
     * Resolve Brand
     *
     * @return string
     */
    protected function getSpeakerBrandAttribute()
    {
        return $this->nomination->brand_label;
    }

    /**
     * Resolve ACTIVE Speaker Name
     *
     * @return string
     */
    protected function getActiveSpeakerNameAttribute()
    {
        return data_get($this->profile, 'preferred_name');
    }

    /**
     * Resolve Speaker City
     *
     * @return string
     */
    protected function getSpeakerCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Resolve Speaker State
     *
     * @return string
     */
    protected function getSpeakerStateAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Resolve Speaker Tier
     *
     * @return string
     */
    protected function getSpeakerTierAttribute()
    {
        return $this->nomination->tier_label;
    }

    /**
     * Resolve Representative
     *
     * @return string
     */
    protected function getRepresentativeAttribute()
    {
        return data_get($this->owner, 'preferred_name');
    }

    /**
     * Resolve Manager
     *
     * @return string
     */
    protected function getDistrictAreaAttribute()
    {
        return data_get($this->parent, 'preferred_name');
    }

    /**
     * Resolve Manager
     *
     * @return string
     */
    protected function getRegionalAttribute()
    {
        return data_get($this->region, 'preferred_name');
    }

    /**
     * Resolve Contract Start Date
     *
     * @return string
     */
    protected function getContractStartDateAttribute()
    {
        # get value
        $value = data_get($this->contract, 'valid_from');
        # format
        return DateFormats::excelDate($value);
    }

    /**
     * Resolve Contract End Date
     *
     * @return string
     */
    protected function getContractEndDateAttribute()
    {
        # get value
        $value = data_get($this->contract, 'valid_to');
        # format
        return DateFormats::excelDate($value);
    }


    /**
     * Resolve Number of Completed Programs
     *
     * @return string
     */
    protected function getNumberOfCompletedProgramsAttribute()
    {
        return $this->completed->count();
    }

    /**
     * Resolve Number of Pending Programs
     *
     * @return string
     */
    protected function getNumberOfPendingProgramsAttribute()
    {
        return $this->upcoming->count();
    }

    /**
     * Resolve Total Programs
     *
     * @return string
     */
    protected function getTotalProgramsAttribute()
    {
        return $this->completed->count()
             + $this->upcoming->count();
    }

    /**
     * Placeholder for client' decision
     *
     * @return void
     */
    protected function getApprovedNotApprovedAttribute()
    {
        return null;
    }

    /**
     * Placeholder for client' decision
     *
     * @return void
     */
    protected function getMedicalAffairsAttribute()
    {
        return null;
    }

    /**
     * Placeholder for client' decision
     *
     * @return void
     */
    protected function getMedicalAffairsConfirmedTierAttribute()
    {
        return null;
    }

    /**
     * Placeholder for client' decision
     *
     * @return void
     */
    protected function getComplianceAttribute()
    {
        return null;
    }

    /**
     * Placeholder for client' decision
     *
     * @return void
     */
    protected function getBrandAttribute()
    {
        return null;
    }

    /**
     * Get Speaker Bureau
     *
     * @return void
     */
    protected function getSpeakerBureauAttribute()
    {
        return $this->profile->speakerBureaus->filter(function($bureau){
            return $bureau->brand->is($this->nomination->brand)
               and $this->nomination->validIntersect($bureau->pivot->valid_from, $bureau->pivot->valid_to);
        })->implode('label',  ', ');
    }
}
