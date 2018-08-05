<?php

namespace Betta\Services\Generator\Streams\Shared;

trait W9Merges
{
    /**
     * Get Saluation from W9
     *
     * @return string
     */
    public function getW9SalutationAttribute()
    {
        return data_get($this->w9, 'salutation') ?: 'Dr.';
    }

    /**
     * Get formal Name from W9
     *
     * @return string
     */
    public function getW9FormalNameAttribute()
    {
        return data_get($this->w9, 'formal_name');
    }

    /**
     * Get Format name with degree from W9
     *
     * @return string
     */
    public function getW9FormalNameDegreeAttribute()
    {
        return data_get($this->w9, 'formal_name_degree');
    }

    /**
     * Get Business name from W9
     *
     * @return string
     */
    public function getW9BusinessNameAttribute()
    {
        return data_get($this->w9, 'formal_business_name');
    }

    /**
     * Get Address Line from W9
     *
     * @return string
     */
    public function getW9AddressLineAttribute()
    {
        return data_get($this->w9, 'form_address');
    }

    /**
     * Get City Address from W9
     *
     * @return string
     */
    public function getW9AddressCityAttribute()
    {
        return data_get($this->w9, 'form_city');
    }

    /**
     * Get Address State from W9
     *
     * @return string
     */
    public function getW9AddressStateAttribute()
    {
        return data_get($this->w9, 'form_state');
    }

    /**
     * Get profile Degree from W9
     *
     * @return string
     */
    public function getW9ProfileDegreeAttribute()
    {
        return data_get($this->w9, 'profile_degree');
    }

    /**
     * Get City State ZIP from W9
     *
     * @return string
     */
    public function getW9CityStateZipAttribute()
    {
        return data_get($this->w9, 'city_state_zip');
    }
}
