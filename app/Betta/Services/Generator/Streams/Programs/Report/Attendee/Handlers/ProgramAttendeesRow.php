<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Attendee\Handlers;

use Betta\Models\Registration;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ProgramAttendeesRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Registration
     */
    protected $registration;

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
        'Account Manager',
        'Profile ID',
        'Brand',
        'Program Type',
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
        'Fax',
        'Attendee Type',
        'Status',
        'Signature',
        'Consumed Food',
        'Government Employee',
        'Horizon Staff',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
        'profile'
    ];

    /**
     * Create new Row instance
     *
     * @param Collection $collection
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
        $this->program = $registration->context;
        $this->profile = $registration->profile;
    }

    /**
     * Get Program Id
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Program start date
     *
     * @return float
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Program account manager
     *
     * @return string
     */
    public function getAccountManagerAttribute()
    {
        return data_get($this->program,'primary_field.preferred_name');
    }

    /**
     * Attendee profile id
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
        return data_get($this->program,'primary_brand.label');
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
     * Attendee customer master id
     *
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return data_get($this->registration,'profile.customer_master_id');
    }

    /**
     * HCP Attendee npi
     *
     * @return string
     */
    public function getNpiAttribute()
    {
        return empty($this->registration->npi) ? data_get($this->profile,'hcpProfile.npi') : $this->registration->npi;
    }

    /**
     * Attendee name
     *
     * @return string
     */
    public function getAttendeeNameAttribute()
    {
        return $this->registration->last_first_name;
    }

    /**
     * Attendee Degree
     *
     * @return string
     */
    public function getDegreeAttribute()
    {
        #if the Registration is a FIELD we don't expect anything
        if($this->registration->is_rep){
            return '';
        }
        # first, try to return the Degree from Registration (could be whatever OTHER was entered)
        if($value = $this->registration->degree){
            return $value;
        }
        # if Degree was empty, try to resolve the Audience Type label
        if($value = $this->registration->audience_type_label){
            return $value;
        }
        #if the Registration is an HCP, try to get the degree from HCP profile
        if($this->registration->is_hcp and $value = data_get($this->profile,'hcpProfile.degree')){
            return $value;
        }
        # default
        return 'OTHER';
    }

    /**
     * Attendee Speciality
     *
     * @return string
     */
    public function getSpecialtyAttribute()
    {
        return $this->registration->specialty ?: data_get($this->profile, 'hcpProfile.specialty');
    }

    /**
     * Address
     *
     * @return string
     */
    public function getAddressAttribute()
    {
        return $this->registration->address_line;
    }

    /**
     * City
     *
     * @return string
     */
    public function getCityAttribute()
    {
        return $this->registration->city;
    }

    /**
     * State
     *
     * @return string
     */
    public function getStateAttribute()
    {
        return $this->registration->state_province;
    }

    /**
     * Postal Code
     *
     * @return string
     */
    public function getZipAttribute()
    {
        return $this->registration->postal_code;
    }

    /**
     * Attendee email
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->registration->email;
    }

    /**
     * Attendee phone number
     *
     * @return string
     */
    public function getPhoneAttribute()
    {
        return $this->registration->phone;
    }

    /**
     * Attendee Fax number
     *
     * @return string
     */
    public function getFaxAttribute()
    {
        return $this->registration->fax;
    }

    /**
     * Registration Bucket
     *
     * @return string
     */
    public function getAttendeeTypeAttribute()
    {
        return $this->registration->registration_bucket;
    }

    /**
     * Registration status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->registration->status_label;
    }

    /**
     * Signature ..
     *
     * @return null
     */
    public function getSignatureAttribute()
    {
        return $this->registration->is_signature_on_file_label;
    }

    /**
     * Has consumed meal
     *
     * @return string | null
     */
    public function getConsumedFoodAttribute()
    {
        return $this->booleanLabel($this->registration->has_consumed_meal);
    }

    /**
     * Is Company Employee
     *
     * @return string | null
     */
    public function getGovernmentEmployeeAttribute()
    {
        return $this->booleanLabel($this->registration->is_government_employee);
    }

    /**
     * Is Company Employee
     *
     * @return string | null
     */
    public function getHorizonStaffAttribute()
    {
        return $this->booleanLabel($this->registration->is_company_emp);
    }

    /**
     * Is office staff
     *
     * @return string | null
     */
    public function getOfficeStaffAttribute()
    {
        return $this->booleanLabel($this->registration->is_office_staff);
    }

    /**
     * label for boolean values
     * @param  boolean $value
     * @param  $default
     * @return string
     */
    protected function booleanLabel($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
