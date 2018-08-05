<?php

namespace Betta\Services\Generator\Streams\Profile\Contract\Generic;

use Carbon\Carbon;
use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class MergeData extends AbstratRowHandler
{
    use Rates;
    use MetaData;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * Reportable items
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
        'last_name',
        'specialty',
        'profile_degree',
        'usd',
        'id',
        'contract_start_date',
        'contract_end_date',
        'salutation',
        'current_date',
        'support_phone',
        'address_city_state_zip',
        'counter_name',
        'counter_title',
        'audio',
        'training',
        'rate_ad_board_chair',
        'rate_ad_board_member',
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
        'max_cap_limit',
        'max_cap_threshold',
        'max_cap_limit_verbose',
        'max_cap_threshold_verbose',
        'work_to_perform',
        'background_technology',
        'other_terms',
        'expiry_date',
        'preferred_name',
        'phone',
        'email',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'w9',
        'profile',
        'rateCard',
        'maxCap',
        'keyed_metas',
    ];

    /**
     * Common Date Format
     *
     * @var string
     */
    protected $dateFormat = 'F j, Y';

    /**
     * Create new class instance
     *
     * @param Betta\Models\Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        # Set hidden attributes
        $this->setAttribute('w9', $contract->w9);
        $this->setAttribute('profile', $contract->profile);
        $this->setAttribute('rateCard', $contract->rateCard);
    }

    /**
     * Get Contract Id
     *
     * @return string
     */
    public function getContractIdAttribute()
    {
        return $this->contract->id;
    }

    /**
     * Get Formal Name
     *
     * @return string
     */
    public function getFormalNameAttribute()
    {
        return data_get($this->w9,'formal_name');
    }

    /**
     * Get Formal Name Degree
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
        return data_get($this->w9,'formal_business_name');
    }

    /**
     * Get Address Line
     *
     * @return string
     */
    public function getAddressLineAttribute()
    {
        return data_get($this->w9,'form_address');
    }

    /**
     * Get Address City
     *
     * @return string
     */
    public function getAddressCityAttribute()
    {
        return data_get($this->w9,'form_city');
    }

    /**
     * Get Address State
     *
     * @return string
     */
    public function getAddressStateAttribute()
    {
        return data_get($this->w9,'form_state');
    }

    /**
     * Get Address Zip
     *
     * @return string
     */
    public function getAddressZipAttribute()
    {
        return data_get($this->w9,'form_zip');
    }

    /**
     * Get Last Name
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return data_get($this->profile,'last_name');
    }

    /**
     * Get Preferred Name
     *
     * @return string
     */
    public function getPreferredNameAttribute()
    {
        return data_get($this->profile,'preferred_name');
    }

    /**
     * Get Phone
     *
     * @return string
     */
    public function getPhoneAttribute()
    {
        return data_get($this->profile,'phone');
    }

    /**
     * Get Phone
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return data_get($this->profile,'email');
    }

    /**
     * Get Specialty
     *
     * @return string
     */
    public function getSpecialtyAttribute()
    {
        return data_get($this->profile,'hcpProfile.degree');
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
     * Get USD character string
     *
     * @return string
     */
    public function getUsdAttribute()
    {
        return '$';
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->contract->id;
    }

    /**
     * Get Contract Start Date
     *
     * @return string
     */
    public function getContractStartDateAttribute()
    {
        return $this->contract->valid_from->format($this->dateFormat);
    }

    /**
     * Get Contract End Date
     *
     * @return string
     */
    public function getContractEndDateAttribute()
    {
        return $this->contract->valid_to->format($this->dateFormat);
    }

    /**
     * Get Salutation
     *
     * @return string
     */
    public function getSalutationAttribute()
    {
        return data_get($this->w9,'salutation') ?: 'Dr.';
    }

    /**
     * Get Current Date
     *
     * @return string
     */
    public function getCurrentDateAttribute()
    {
        return Carbon::today()->format($this->dateFormat);
    }

    /**
     * Get Support Phone
     *
     * @return string
     */
    public function getSupportPhoneAttribute()
    {
        return config('fls.support_phone');
    }

    /**
     * Get Address City State Zip
     *
     * @return string
     */
    public function getAddressCityStateZipAttribute()
    {
        return data_get($this->w9,'city_state_zip');
    }

    /**
     * Counter-Signer Name
     *
     * @return string
     */
    public function getCounterNameAttribute()
    {
        return data_get($this->contract,'countersigner.preferred_name', '');
    }

    /**
     * Counter-Signer Title
     *
     * @return string
     */
    public function getCounterTitleAttribute()
    {
        return data_get($this->contract,'countersigner.title', '');
    }
}
