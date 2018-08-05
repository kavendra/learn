<?php

namespace Betta\Services\Generator\Streams\Shared;

use Carbon\Carbon;

trait ContractMerges
{
    /**
     * Get Contract ID
     *
     * @return string
     */
    public function getContractIdAttribute()
    {
        return data_get($this->contract, 'id');
    }

    /**
     * Get Label for the contract
     *
     * @return string
     */
    public function getContractLabelAttribute()
    {
        return data_get($this->contract, 'contract_type_label');
    }
    /**
     * Get Period of the contract
     *
     * @return string
     */
    public function getContractPeriodAttribute()
    {
        return data_get($this->contract, 'period');
    }

    /**
     * Get Start Date
     *
     * @return string
     */
    public function getContractFromAttribute()
    {
        return $this->formattedDate(data_get($this->contract, 'valid_from'), 'F j, Y');
    }

    /**
     * Get Start Date
     *
     * @alias
     * @return string
     */
    public function getContractStartDateAttribute()
    {
        return $this->getContractFromAttribute();
    }

    /**
     * Get End Date
     *
     * @return string
     */
    public function getContractToAttribute()
    {
        return $this->formattedDate(data_get($this->contract, 'valid_to'), 'F j, Y');
    }

    /**
     * Get Start Date
     *
     * @alias
     * @return string
     */
    public function getContractEndDateAttribute()
    {
        return $this->getContractToAttribute();
    }

    /**
     * Format the date
     *
     * @param  mixed $date
     * @param  string $format
     * @return string | null
     */
    protected function formattedDate($date, $format = 'm/d/Y')
    {
        return empty($date) ? '' : Carbon::parse($date)->format($format);
    }
}
