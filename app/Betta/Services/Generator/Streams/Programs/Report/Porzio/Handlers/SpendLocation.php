<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait SpendLocation
{
    /**
     * Location: Name of the Location
     *
     * @return string
     */
    public function getSpendLocationOrDestinationNameAttribute()
    {
        return data_get($this->location, 'location_name');
    }

    /**
     * Location: Address Line 1
     *
     * @return string
     */
    public function getSpendLocationOrDestinationAddress1Attribute()
    {
        return data_get($this->location, 'line_1');
    }

    /**
     * Location: Address Line 2
     *
     * @return string
     */
    public function getSpendLocationOrDestinationAddress2Attribute()
    {
        return data_get($this->location, 'line_2');
    }

    /**
     * Location: Address City
     *
     * @return string
     */
    public function getSpendLocationOrDestinationCityAttribute()
    {
        return data_get($this->location, 'city');
    }

    /**
     * Location: Address Status
     *
     * @return string
     */
    public function getSpendLocationOrDestinationStateAttribute()
    {
        return data_get($this->location, 'state_province');
    }

    /**
     * Location: Address ZIP code
     *
     * @return string
     */
    public function getSpendLocationOrDestinationZipCodeAttribute()
    {
        return data_get($this->location, 'postal_code');
    }

    /**
     * Location: Address ZIP Extension code
     *
     * @return string
     */
    public function getSpendLocationOrDestinationZipCodeExtAttribute()
    {
        return null;
    }

    /**
     * Location: Spend Location or Destintion
     *
     * @return string
     */
    public function getSpendLocationOrDestinationCountryAttribute()
    {
        return data_get($this->location, 'country') ?: 'US';
    }
}
