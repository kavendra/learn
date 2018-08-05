<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;
use Carbon\Carbon;

class PrescribersAttendeesRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Brand',
        'Program Type',
        'Program Date',
        'Program Time',
        'Program Status',
        'Reconciled',
        'Representative Name',
        'District Manager Name',
        'Presentation Topic',
        'Total prescribers',
        'Target Attendees',
        'Total Attendance',
        'Attended Rep',
        'Location Name',
        'Location Address Line 1',
        'Location Address Line 2',
        'Location Address City',
        'Location Address State Province',
        'Location Address Postal Code',
        'Speaker Name',
        'AV Cost Actual',
        'FB Cost Actual',
        'Room Rental Actual',
        'FB Per Person',
        'Program Manager'
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'field',
        'address',
        'av_category_costs',
        'fb_costs',
        'room_rental_costs',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Program ID
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Program brand
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->program,'primary_brand.label');
    }

    /**
     * Program type
     *
     * @return string | null
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

     /**
     * Formatted Date of the Program
     *
     * @return string | null
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Formatted Time of the Program (formatting is done by Excel)
     *
     * @return string | null
     */
    public function getProgramTimeAttribute()
    {
        return excel_date($this->program->start_date);
    }


    /**
     * Program Status
     *
     * @return string | null
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Yes if the Progtram is reconciled, otherwise No
     *
     * @return string | null
     */
    public function getReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
    }


    /**
     * Program Primary Field
     *
     * @access hidden
     * @return Profile | null
     */
    public function getFieldAttribute()
    {
        return $this->program->primary_field;
    }

    /**
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getRepresentativeNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Primary Field's Name' Manager' name
     *
     * @return string | null
     */
    public function getDistrictManagerNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getPresentationTopicAttribute()
    {
        return $this->program->title;
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getTotalPrescribersAttribute()
    {
        return $this->program->hcp_registrations->filter(function($registration){
                    return  in_array($registration->degree, ['MD', 'DO', 'NP', 'PA'] )
                    OR in_array($registration->audience_type_label, ['MD', 'DO', 'NP', 'PA'] );
                })->count();
    }

    /**
     * Audience Types
     *
     * @return string | null
     */
    public function getTargetAttendeesAttribute()
    {
        return $this->program->audienceTypes->implode('label', ', ');
    }

    /**
     * Total Attendee count
     *
     * @return string | null
     */
    public function getTotalAttendanceAttribute()
    {
        return $this->program->attendee_count_hcp;
    }

    /**
     * Attendnace Field
     *
     * @return string | null
     */
    public function getAttendedRepAttribute()
    {
        return $this->program->attendee_count_field;
    }

    /**
     * Return The Location Address
     * @access hidden
     * @return Address
     */
    public function getAddressAttribute()
    {
        return $this->program->address;
    }

    /**
     * Location: Name
     *
     * @return string | null
     */
    public function getLocationNameAttribute()
    {
        return data_get($this->address, 'name');
    }

    /**
     * Location: Line 1
     *
     * @return string | null
     */
    public function getLocationAddressLine1Attribute()
    {
        return data_get($this->address, 'line_1');
    }

    /**
     * Location: Line 2
     *
     * @return string | null
     */
    public function getLocationAddressLine2Attribute()
    {
        return data_get($this->address, 'line_2');
    }

    /**
     * Location: City
     *
     * @return string | null
     */
    public function getLocationAddressCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Location: State
     *
     * @return string | null
     */
    public function getLocationAddressStateProvinceAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Location: ZIP
     *
     * @return string | null
     */
    public function getLocationAddressPostalCodeAttribute()
    {
        return data_get($this->address, 'postal_code');
    }

    /**
     * Program Speakers' names
     *
     * @return string
     */
    protected function getSpeakerNameAttribute()
    {
        return $this->program->primarySpeakers->implode('profile.preferred_name', ', ');
    }

    /**
     * Resolve AV Costs from Progran
     *
     * @access hidden
     * @return Collection
     */
    public function getAvCategoryCostsAttribute()
    {
        return$this->program->av_category_costs;
    }

    /**
     * Real AV Cost
     *
     * @return string | null
     */
    public function getAVCostActualAttribute()
    {
        return $this->av_category_costs->sum('real');
    }

    /**
     * Resolve FB Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getFbCostsAttribute()
    {
        return$this->program->fb_costs;
    }

    /**
     * Real FB Cost
     *
     * @return string | null
     */
    public function getFBCostActualAttribute()
    {
        return $this->fb_costs->sum('real');
    }

    /**
     * Resolve Room Rental Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getRoomRentalCostsAttribute()
    {
        return$this->program->room_rental_costs;
    }

    /**
     * Actual Room Rental Costs from Program
     *
     * @return Collection
     */
    public function getRoomRentalActualAttribute()
    {
        return$this->room_rental_costs->sum('real');
    }

    /**
     * FB per Person
     *
     * @return string | null
     */
    public function getFBPerPersonAttribute()
    {
        return $this->program->fb_per_person;
    }

    /**
     * Primary PM
     *
     * @return string | null
     */
    public function getProgramManagerAttribute()
    {
        return data_get($this->program->primary_pm, 'preferred_name');
    }


}
