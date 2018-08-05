<?php

namespace Betta\Services\Generator\Streams\Profile\Contract;

use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Betta\Foundation\Handlers\AbstractTransformer;
use Betta\Services\Generator\Streams\Shared\CommonMerges;
use Betta\Services\Generator\Streams\Shared\ContractMerges;

class MergeData extends AbstractTransformer
{
    use RateMerges;
    use CommonMerges;
    use ContractMerges;

    /**
     * Merge Values
     *
     * @var Array
     */
    protected $keys = [
        'contract_id',
        'formal_name',
        'formal_name_degree',
        'business_name',
        'address_line',
        'address_city',
        'address_state',
        'address_zip',
        'city_state_zip',
        'last_name',
        'specialty',
        'profile_degree',
        'contract_start_date',
        'contract_end_date',
        'usd',
        'salutation',
        'current_date',
        'support_phone',
        'address_city_state_zip',
        'audio',
        'training',
        'congress_activity',
        'first_rate_up_200',
        'first_rate_up_1000',
        'first_rate_up_3000',
        'first_rate_up_7000',
        'first_rate_over_7000',
        'multi_rate2_up_200',
        'multi_rate2_up_1000',
        'multi_rate2_up_3000',
        'multi_rate2_up_7000',
        'multi_rate2_over_7000',
        'multi_rate3_up_200',
        'multi_rate3_up_1000',
        'multi_rate3_up_3000',
        'multi_rate3_up_7000',
        'multi_rate3_over_7000',
        'live_training_rate_up_200',
        'live_training_rate_up_1000',
        'live_training_rate_up_3000',
        'live_training_rate_up_7000',
        'live_training_rate_over_7000',
    ];

    /**
     * Helper values that should not be visible in resulting array
     *
     * @var array
     */
    protected $hidden = [
        'w9',
        'profile',
        'rateCard',
    ];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * Create new class
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Resolve W9
     *
     * @access hidden
     * @return string
     */
    public function getW9Attribute()
    {
        return data_get($this->contract,'w9');
    }

    /**
     * Resolve Profile
     *
     * @access hidden
     * @return string
     */
    public function getProfileAttribute()
    {
        return data_get($this->contract,'profile');
    }

    /**
     * Resolve RateCard
     *
     * @access hidden
     * @return string
     */
    public function getRateCardAttribute()
    {
        return data_get($this->contract,'rateCard');
    }

    /**
     * Get Format Name from
     *
     * @return string
     */
    public function getFormalNameAttribute()
    {
        return data_get($this->w9,'formal_name');
    }

    /**
     * Get Format Name with Degree
     *
     * @return string
     */
    public function getFormalNameDegreeAttribute()
    {
        return data_get($this->w9,'formal_name_degree');
    }

    /**
     * Get Business Name
     *
     * @return string
     */
    public function getBusinessNameAttribute()
    {
        return data_get($this->w9,'business_name');
    }

    /**
     * Get Address: Line
     *
     * @return string
     */
    public function getAddressLineAttribute()
    {
        return data_get($this->w9,'form_address');
    }

    /**
     * Get Address: City
     *
     * @return string
     */
    public function getAddressCityAttribute()
    {
        return data_get($this->w9,'form_city');
    }

    /**
     * Get Address: State
     *
     * @return string
     */
    public function getAddressStateAttribute()
    {
        return data_get($this->w9,'form_state');
    }

    /**
     * Get Address: ZIP
     *
     * @return string
     */
    public function getAddressZipAttribute()
    {
        return data_get($this->w9,'form_zip');
    }

    /**
     * Get Profile Degree
     *
     * @return string
     */
    public function getProfileDegreeAttribute()
    {
        return data_get($this->w9,'profile_degree');
    }

    /**
     * Get salutation
     *
     * @return string
     */
    public function getSalutationAttribute()
    {
        return data_get($this->w9,'salutation') ?: 'Dr.';
    }

    /**
     * Get Address: City State ZIP
     *
     * @alias
     * @return string
     */
    public function getCityStateZipAttribute()
    {
        return $this->getAddressCityStateZipAttribute();
    }

    /**
     * Get Address: City State ZIP
     *
     * @return string
     */
    public function getAddressCityStateZipAttribute()
    {
        return data_get($this->w9,'city_state_zip');
    }

    /**
     * Get Profile::First Name
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return data_get($this->profile,'fist_name');
    }

    /**
     * Get Profile::Last Name
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return data_get($this->profile,'last_name');
    }

    /**
     * Get Profile::Middle Name
     *
     * @return string
     */
    public function getMiddleNameAttribute()
    {
        return data_get($this->profile,'middle_name');
    }

    /**
     * Get specialty
     *
     * @return string
     */
    public function getSpecialtyAttribute()
    {
        return data_get($this->profile,'hcpProfile.degree');
    }
}
