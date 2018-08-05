<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\ActiveSpeakerContract\Handlers;

use Betta\Models\Nomination;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ActiveSpeakerContractHandler extends AbstratRowHandler
{
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
        'Brand',
        'Speaker Last Name',
        'Speaker First Name',
        'Speaker Degree',
        'Title',
        'Speaker Specialty',
        'Tier',
        'Bureau',
        'Address Line 1',
        'Address Line 2',
        'Address City',
        'Address State',
        'Address Zip Code',
        'Speaker Email',
        'Admin Name',
        'Admin Email',
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
     * Get Customer Master ID
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return $this->nomination->profile->customer_master_id;
    }

    /**
     * Get Brand
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->nomination, 'brand_label');
    }

    /**
     * Get Speaker Last Name
     * @return string
     */
    public function getSpeakerLastNameAttribute()
    {
        return data_get($this->nomination->profile, 'last_name');
    }

    /**
     * Get Speaker First Name
     * @key first_name
     *
     * @return string
     */
    public function getSpeakerFirstNameAttribute()
    {
        return data_get($this->nomination->profile, 'first_name');
    }

    /**
     * Get Degree of Speaker
     * @key degree
     *
     * @return string
     */
    public function getSpeakerDegreeAttribute()
    {
        return data_get($this->nomination->profile, 'hcpProfile.degree');
    }

    /**
     * Get Title
     * @key preferred_signature
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return data_get($this->nomination->profile->speakerProfile, 'preferred_signature');
    }

    /**
     * Get Speciality of HCP
     * @key speciality
     *
     * @return string
     */
    public function getSpeakerSpecialtyAttribute()
    {
        return data_get($this->nomination->profile, 'hcpProfile.specialty');
    }

    /**
     * Get Tier
     *
     * @return decimal
     */
    protected function getTierAttribute()
    {
        return data_get($this->nomination, 'tier_label');
    }

    /**
     * Get Speaker Bureau
     *
     * @return void
     */
    public function getBureauAttribute()
    {
       return data_get($this->nomination->bureau, 'label');
    }

    /**
     * Get line_1 from primary address
     * @key line_1
     *
     * @return string
     */
    public function getAddressLine1Attribute()
    {
        return data_get($this->nomination->profile, 'preferredAddress.line_1');
    }

    /**
     * Get line_2 from primary address
     * @key line_2
     *
     * @return string
     */
    public function getAddressLine2Attribute()
    {
        return data_get($this->nomination->profile, 'preferredAddress.line_2');
    }

    /**
     * Get city from primary address
     * @key city
     * @return string
     */
    public function getAddressCityAttribute()
    {
        return data_get($this->nomination->profile, 'preferredAddress.city');
    }

    /**
     * Get state from primary address
     * @key state_province
     *
     * @return string
     */
    public function getAddressStateAttribute()
    {
        return data_get($this->nomination->profile, 'preferredAddress.state_province');
    }

    /**
     * Get postal code from primary address
     * @key postal_code
     *
     * @return string
     */
    public function getAddressZipCodeAttribute()
    {
        return data_get($this->nomination->profile, 'preferredAddress.postal_code');
    }

     /**
     * Get speaker email
     * @key primary_email
     *
     * @return string
     */
    public function getSpeakerEmailAttribute()
    {
        return data_get($this->nomination->profile, 'email');
    }

     /**
     * Get admin name
     * @return string
     */
    public function getAdminNameAttribute()
    {
        return $this->nomination->profile->assistants->implode('assistant_name', ', ');
    }

     /**
     * Get admin email
     * @return string
     */
    public function getAdminEmailAttribute()
    {
        return $this->nomination->profile->assistants->implode('email', ', ');
    }

}
