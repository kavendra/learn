<?php

namespace Betta\Services\Generator\Streams\Programs\Report\SpeakerProgramTracker\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
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
        'Brand Name',
        'Presentation Topic',
        'Program ID',
        'Program Status',
        'Sales Representative',
        'Manager',
        'National Sales Director',
        'Program Type',
        'Speaker Name',
        'Program Date',
        'Program Time',
        'Venue Name',
        'Venue City',
        'Venue State',
        'Total Attendees',
        'Total Horizon Employees',
        'Total Program Cost',
        'Food Beverage Receipt Received',
        'Attendee Signin Sheet Received',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'year',
        'nsd',
        'address',
        'nsd_profile',
        'manager_profile',
        'address_profile',
        'is_reconciled',
        'cancel_comments',
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
     * Resolve year from the Carbon instance of the start date
     *
     * @access hidden
     * @return int
     */
    protected function getYearAttribute()
    {
        return $this->program->start_date->year;
    }

    /**
     * Get Brand Name of Program
     *
     * @return string
     */
    public function getBrandNameAttribute()
    {
        return $this->program->brands->implode('label', ' / ');
    }

    /**
     * Program Presentation Topic
     *
     * @return string
     */
    public function getPresentationTopicAttribute()
    {
        return $this->program->title;
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
     * Program Status
     *
     * @return string
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Program ID
     *
     * @return string
     */
    public function getIsReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
    }

    /**
     * Primary Fiel
     *
     * @return string | null
     */
    public function getSalesRepresentativeAttribute()
    {
        return data_get($this->program,'primary_field.preferred_name');
    }

    /**
     * Fetching Manager Details
     *
     * @access hidden
     * @return Profile | null
     */
    public function getManagerProfileAttribute()
    {
        return data_get($this->program,'primary_field.parent');
    }

    /**
     * Fetching National Sales Director Details
     *
     * @access hidden
     * @return Profile | null
     */
    public function getNsdProfileAttribute()
    {
        return data_get($this->manager_profile,'parent');
    }
    /**
     * Get manager of Program
     *
     * @return string
     */
    public function getManagerAttribute()
    {
        return data_get($this->manager_profile,'preferred_name');
    }

    /**
     * Get National Sales Director of Program
     *
     * @return string
     */
    public function getNationalSalesDirectorAttribute()
    {
        return data_get($this->nsd_profile,'preferred_name');
    }

    /**
     * Get type of Program
     *
     * @return string
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Primary Speakers
     *
     * @return string
     */
    public function getSpeakerNameAttribute()
    {
        return $this->program->primary_speakers->implode('profile.preferred_name', ', ');
    }

    /**
     * Program Date
     *
     * @return float
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get time of Program
     *
     * @return string
     */
    public function getProgramTimeAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Location Name
     *
     * @return string
     */
    public function getVenueNameAttribute()
    {
        return data_get($this->program,'primary_location.name');
    }

    /**
     * Get Address
     *
     * @access hidden
     * @return Address | null
     */
    public function getAddressAttribute()
    {
        return data_get($this->program,'address');
    }

    /**
     * Program Location City
     *
     * @return string
     */
    public function getVenueCityAttribute()
    {
        return data_get($this->address,'city');
    }

    /**
     * Program Location State
     *
     * @return string
     */
    public function getVenueStateAttribute()
    {
        return data_get($this->address,'state_province');
    }

    /**
     * Total Attendees
     *
     * @return string
     */
    public function getTotalAttendeesAttribute()
    {
        return $this->program->registrations->sum('attended');
    }

    /**
     * Total Horizon Employees
     *
     * @return string
     */
    public function getTotalHorizonEmployeesAttribute()
    {
        return $this->program->field_registrations->sum('attended');
    }

    /**
     * Total Program Cost
     *
     * @return string
     */
    public function getTotalProgramCostAttribute()
    {
        return $this->program->costs->sum('calculated');
    }

    /**
     * True if F&B Receipt Received
     *
     * @return string
     */
    public function getFoodBeverageReceiptReceivedAttribute()
    {
        return data_get($this->program,'is_fb_receipt');
    }

    /**
     * True if the Sign in SHeet recieved
     *
     * @return string
     */
    public function getAttendeeSigninSheetReceivedAttribute()
    {
        return data_get($this->program,'closeout.signin_sheet_recieved');
    }

    /**
     * Program Cancel Comments
     *
     * @return string
     */
    public function getCancelCommentsAttribute()
    {
        return data_get($this->program,'cancellation_notes');
    }
}
