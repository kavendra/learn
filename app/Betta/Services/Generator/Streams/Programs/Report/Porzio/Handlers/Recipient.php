<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait Recipient
{
    /**
     * Recipient: CMID?
     *
     * @return string|null
     */
    public function getRecipientNumberAttribute()
    {
        return data_get($this->recipient, 'customer_master_id');
    }

    /**
     * Recipient: License State
     *
     * @return string|null
     */
    public function getRecipientLicenseStateAttribute()
    {
        return data_get($this->recipient, 'license_state');
    }

    /**
     * Recipient: SLN
     *
     * @return string|null
     */
    public function getRecipientStateLicenseNumberAttribute()
    {
        return data_get($this->recipient, 'license_number');
    }

    /**
     * Recipient: DEA
     *
     * @return string|null
     */
    public function getRecipientDeaNumberAttribute()
    {
        return data_get($this->recipient, 'dea_number');
    }

    /**
     * Recipient: Validated NPI
     *
     * @return string|null
     */
    public function getRecipientNpiNumberAttribute()
    {
        # Get the
        $npi = data_get($this->recipient, 'porzio_npi');
        # Only return NPI if it is valid
        return strlen($npi) == 10 ? $npi : null;
    }

    /**
     * Recipient: ME Number
     *
     * @return string|null
     */
    public function getRecipientMeNumberAttribute()
    {
        return data_get($this->recipient, 'me_number');
    }

    /**
     * Recipient: Last Name
     *
     * @return string
     */
    public function getRecipientLastNameAttribute()
    {
        return data_get($this->recipient, 'last_name');
    }

    /**
     * Recipient: First Name
     *
     * @return string
     */
    public function getRecipientFirstNameAttribute()
    {
        return data_get($this->recipient, 'first_name');
    }

    /**
     * Recipient: Middle Name
     *
     * @return string
     */
    public function getRecipientMiddleNameAttribute()
    {
        return data_get($this->recipient, 'middle_name');
    }

    /**
     * Recipient: Title
     *
     * @return string
     */
    public function getRecipientTitleAttribute()
    {
        return data_get($this->recipient, 'title');
    }

    /**
     * Recipient: Suffix Name
     *
     * @return string
     */
    public function getRecipientSuffixNameAttribute()
    {
        return data_get($this->recipient, 'suffix');
    }

    /**
     * Recipient: Designation
     *
     * @return string
     */
    public function getRecipientDesignationAttribute()
    {
        return data_get($this->recipient, 'porzio_degree');
    }

    /**
     * Recipient: Specialty
     *
     * @return string
     */
    public function getRecipientSpecialtyAttribute()
    {
        return data_get($this->recipient, 'porzio_specialty');
    }

    /**
     * Recipient: Type
     * Same as Designation
     *
     * @see https://frictionless.teamwork.com/#tasks/8134735
     * @return string|null
     */
    public function getRecipientTypeAttribute()
    {
        return data_get($this->recipient, 'porzio_degree');
    }

    /**
     * Recipient: Territory
     *
     * @return null
     */
    public function getRecipientTerritoryAttribute()
    {
        return null;
    }

    /**
     * Recipient: Affiliated Institution
     *
     * @return string|null
     */
    public function getRecipientAffiliatedInstitutionNameAttribute()
    {
        return null;
    }


    /**
     * ReturnRecipient: Tax ID
     *
     * @return string|null
     */
    public function getTaxIdNumberAttribute()
    {
        return null;
    }

    /**
     * Enumerated value for the number of recipients
     *
     * @return int
     */
    public function getTotalRecipientsAttribute()
    {
        return 1;
    }
}
