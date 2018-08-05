<?php

namespace Betta\Services\Generator\Streams\Profile;

use Carbon\Carbon;
use Betta\Models\Profile;

trait MergesProfileData
{
    /**
     * Use Profile to resolve the Profile Id
     *
     * @return string | null
     */
    public function getProfileIdAttribute()
    {
        return data_get($this->profile, 'id');
    }

    /**
     * Use Profile to resolve the Customer Master Id
     *
     * @return string | null
     */
    public function getProfileCustomerMasterIdAttribute()
    {
        return data_get($this->profile, 'customer_master_id');
    }

    /**
     * Use Profile to resolve the Name: First
     *
     * @return string | null
     */
    public function getProfileFirstNameAttribute()
    {
        return data_get($this->profile, 'first_name');
    }

    /**
     * Use Profile to resolve the Name: Last
     *
     * @return string | null
     */
    public function getProfileLastNameAttribute()
    {
        return data_get($this->profile, 'last_name');
    }

    /**
     * Use Profile to resolve the Name: Preferred
     *
     * @return string | null
     */
    public function getProfilePreferredNameAttribute()
    {
        return data_get($this->profile, 'preferred_name');
    }

    /**
     * Use Profile to resolve the Name: Preferred + Degree
     *
     * @return string | null
     */
    public function getProfilePreferredNameDegreeAttribute()
    {
        return data_get($this->profile, 'preferred_name_degree');
    }

    /**
     * Use Profile to resolve the Address:
     *
     * @return string | null
     */
    public function getProfileStreetAddressAttribute()
    {
        return data_get($this->profile, 'preferred_address.street_address');
    }

    /**
     * Use Profile to resolve the Address:
     *
     * @return string | null
     */
    public function getProfileCityStateAttribute()
    {
        return data_get($this->profile, 'preferred_address.city_state');
    }

    /**
     * Use Profile to resolve the Address:
     *
     * @return string | null
     */
    public function getProfileCityStateZipAttribute()
    {
        return data_get($this->profile, 'preferred_address.city_state_zip');
    }
}
