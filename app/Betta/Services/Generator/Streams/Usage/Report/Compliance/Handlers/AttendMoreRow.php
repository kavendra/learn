<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Program;
use Betta\Models\Registration;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class AttendMoreRow extends AbstratRowHandler
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
        'Program Date',
        'Representative',
        'Profile ID',
        'Brand',
        'Presentation Topic',
        'Status',
        'Customer Master ID',
        'NPI',
        'Attendee Name',
        'Degree',
        'Specialty',
        'Address',
        'City',
        'State',
        'Zip',
        'Email',
        'Phone',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
        'field',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Program
     *
     * @access hidden
     * @return Program | null
     */
    public function getProgramAttribute()
    {
        return data_get($this->registration,'program');
    }

    /**
     * Program Primary Field
     *
     * @access hidden
     * @return Profile | null
     */
    public function getFieldAttribute()
    {
        return data_get($this->registration,'program.primary_field');
    }

    /**
     * Return The Location Address
     * @access hidden
     * @return Address
     */
    public function getAddressAttribute()
    {
        return $this->registration->address_line;
    }

    /**
     * Program ID
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return data_get($this->registration,'program.id');
    }

    /**
     * Formatted Date of the Program
     *
     * @return string | null
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->registration->program->start_date);
    }

    /**
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getRepresentativeAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Profile ID
     *
     * @return string
     */
    public function getProfileIdAttribute()
    {
        return $this->registration->profile_id;
    }

    /**
     * Program brand
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->registration->program,'primary_brand.label');
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getPresentationTopicAttribute()
    {
        return data_get($this->registration,'program.title');
    }

    public function getStatusAttribute()
    {
        return data_get($this->registration,'registrationStatus.label');
    }

    public function getCustomerMasterIdAttribute()
    {
        return data_get($this->registration,'profile.customer_master_id');
    }

    public function getNPIAttribute()
    {
        return $this->registration->npi;
    }

    public function getAttendeeNameAttribute()
    {
        return $this->registration->preferred_name;
    }

    public function getDegreeAttribute()
    {
        return $this->registration->degree;
    }

    public function getSpecialtyAttribute()
    {
        return $this->registration->specialty;
    }

    public function getCityAttribute()
    {
        return $this->registration->city;
    }

    public function getStateAttribute()
    {
        return $this->registration->state_province;
    }

    public function getZipAttribute()
    {
        return $this->registration->postal_code;
    }

    public function getEmailAttribute()
    {
        return $this->registration->email;
    }

    public function getPhoneAttribute()
    {
        return $this->registration->phone;
    }
}
