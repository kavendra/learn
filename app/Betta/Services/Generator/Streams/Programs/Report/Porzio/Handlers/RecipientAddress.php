<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

use Betta\Helpers\Strings;

trait RecipientAddress
{
        /**
     * Recipient Address: Line 1
     *
     * @return string|null
     */
    public function getRecipientAddressLine1Attribute()
    {
        return data_get($this->recipient_address, 'line_1');
    }

    /**
     * Recipient Address: Line 2
     *
     * @return string|null
     */
    public function getRecipientAddressLine2Attribute()
    {
        return data_get($this->recipient_address, 'line_2');
    }

    /**
     * Recipient Address: Line 3
     *
     * @return string|null
     */
    public function getRecipientAddressLine3Attribute()
    {
        return data_get($this->recipient_address, 'line_3');
    }

    /**
     * Recipient Address: City
     *
     * @return string|null
     */
    public function getRecipientCityAttribute()
    {
        return data_get($this->recipient_address, 'city');
    }

    /**
     * Recipient Address: State
     *
     * @return string|null
     */
    public function getRecipientStateAttribute()
    {
        return data_get($this->recipient_address, 'state_province');
    }

    /**
     * Recipient Address: ZIP Code
     *
     * @return string|null
     */
    public function getRecipientZipCodeAttribute()
    {
        return data_get($this->recipient_address, 'postal_code');
    }

    /**
     * Recipient Address: ZIP Code extension
     *
     * @return string|null
     */
    public function getRecipientZipCodeExtAttribute()
    {
        return null;
    }

    /**
     * Recipient Address: Country
     *
     * @return string|null
     */
    public function getRecipientCountryAttribute()
    {
        return data_get($this->recipient_address, 'country') ?: 'US';
    }

    /**
     * Recipient Address: Phone
     *
     * @return string|null
     */
    public function getRecipientPhoneNumberAttribute()
    {
        return Strings::numbersOnly(data_get($this->recipient_address, 'phone'));
    }

    /**
     * Recipient Address: Fax
     *
     * @return string|null
     */
    public function getRecipientFaxNumberAttribute()
    {
        return Strings::numbersOnly(data_get($this->recipient_address, 'fax'));
    }

    /**
     * Recipient Address: Email
     *
     * @return string|null
     */
    public function getRecipientEmailAttribute()
    {
        return data_get($this->recipient_address, 'email');
    }

}
