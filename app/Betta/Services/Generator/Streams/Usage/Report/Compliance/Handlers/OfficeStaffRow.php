<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Registration;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class OfficeStaffRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Registration
     */
    protected $registration;

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
        'Program Type',
        'Speaker Name',
        'Speaker Degree',
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
        'field',
        'profile',
        'program',
        'programAddress',
    ];

    /**
     * Create new Row instance
     *
     * @param Registration $registration
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * get Registration model of HCP registration
     *
     * @access hidden
     * @return Registration | null
     */
    public function getProgramAttribute()
    {
        return data_get($this->registration, 'program');
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
     * get Profile of hcp registrations
     *
     * @access hidden
     * @return Profile | null
     */
    public function getProfileAttribute()
    {
        return data_get($this->registration, 'profile');
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
     * Speaker Name
     *
     * @return string | null
     */
    public function getSpeakerNameAttribute()
    {
        return data_get($this->program->primary_speakers->first(), 'profile.preferred_name');
    }

    /**
     * Primary Speaker: Degree
     *
     * @return string | null
     */
    public function getSpeakerDegreeAttribute()
    {
        return data_get($this->program->primary_speakers->first(), 'profile.hcpProfile.degree');
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
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getRepresentativeAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getProfileIdAttribute()
    {
        return data_get($this->profile, 'id');
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
     * Program Status
     *
     * @return string | null
     */
    public function getStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Cutomer master id of registration
     *
     * @return string | null
     */
    public function getCustomerMasterIdAttribute()
    {
        return data_get($this->profile, 'customer_master_id');
    }

    /**
     * NPI of registration
     *
     * @return string | null
     */
    public function getNPIAttribute()
    {
        return object_get($this->registration, 'npi');
    }

    /**
     * Attendee name
     *
     * @return string | null
     */
    public function getAttendeeNameAttribute()
    {
        return object_get($this->registration, 'preferred_name');
    }

    /**
     * Degree
     *
     * @return string | null
     */
    public function getDegreeAttribute()
    {
        return object_get($this->registration, 'porzio_degree');
    }

    /**
     * Speciality
     *
     * @return string | null
     */
    public function getSpecialtyAttribute()
    {
        return object_get($this->registration, 'specialty');
    }

    /**
     * Return The Location Address
     * @return Address
     */
    public function getProgramAddressAttribute()
    {
        return $this->program->address;
    }

    /**
     * Return The Location Address
     * @return Address
     */
    public function getAddressAttribute()
    {
        return data_get($this->programAddress,'name');
    }

    /**
     * Location: City
     *
     * @return string | null
     */
    public function getCityAttribute()
    {
        return data_get($this->programAddress,'city');
    }

    /**
     * Location: State
     *
     * @return string | null
     */
    public function getStateAttribute()
    {
        return data_get($this->programAddress, 'state_province');
    }

    /**
     * Location: Zip
     *
     * @return string | null
     */
    public function getZipAttribute()
    {
        return data_get($this->programAddress, 'postal_code');
    }

    /**
     * Location: Email
     *
     * @return string | null
     */
    public function getEmailAttribute()
    {
        return data_get($this->programAddress, 'email');
    }

    /**
     * Location: Phone
     *
     * @return string | null
     */
    public function getPhoneAttribute()
    {
        return data_get($this->programAddress, 'phone');
    }

}
