<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\SpeakerList\Handlers;

use Betta\Models\Nomination;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class NominationRow extends AbstratRowHandler
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
        'customer_master_id',
        'npi',
        'last_name',
        'first_name',
        'preferred_name',
        'degree',
        'specialty',
        'brand_label',
        'classifications',
        'institution',
        'line_1',
        'line_2',
        'city',
        'state_province',
        'postal_code',
        'primary_email',
        'primary_phone',
        'fax',
        'rep_territory_id',
        'rep_department',
        'rep_name',
        'rep_email',
        'manager_territory_id',
        'manager_name',
        'manager_email',
        'manager_parent_name',
        'manager_parent_email',
        'status_label',
        'expired_at',
        'background_completed_at',
        'contract_status',
        'contract_created_at',
        'contract_valid_from',
        'contract_valid_to',
        'w9_created_at',
        'w9_status',
        'w9_signed_at',
        'compliance_training_completed_at',
        'speaker_dial_in_phone_number',
        'speaker_dial_in_host_code',
        'speaker_dial_in_attendee_code',
        'speaker_travel_distance',
        'speaker_willing_to_fly',
        'speaker_program_types',
        'speaker_days_of_week',
        'blocked_days',
        'max_hono',
        'tier',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile',
        'owner',
        'manager',
        'latest_contract',
        'speaker_profile',
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
     * Profile of Nomination
     *
     * @see $this->hidden
     * @return Profile | null
     */
    public function getProfileAttribute()
    {
        return $this->nomination->profile;
    }

    /**
     * Get Customer Master ID
     * @key customer_master_id
     *
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return $this->profile->customer_master_id;
    }

    /**
     * Get Customer NPI ID
     * @key npi
     *
     * @return string
     */
    public function getNpiAttribute()
    {
        return data_get($this->profile,'hcpProfile.npi');
    }

    /**
     * Get Customer Last Name
     * @key last_name
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return $this->profile->last_name;
    }

    /**
     * Get Customer First Name
     * @key first_name
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return $this->profile->first_name;
    }

    /**
     * Get Customer Preferred Name
     * @key preferred_name
     *
     * @return string
     */
    public function getPreferredNameAttribute()
    {
        return $this->profile->preferred_name;
    }

    /**
     * Get Degree of HCP
     * @key degree
     *
     * @return string
     */
    public function getDegreeAttribute()
    {
        return data_get($this->profile,'hcpProfile.degree');
    }

    /**
     * Get Speciality of HCP
     * @key speciality
     *
     * @return string
     */
    public function getSpecialtyAttribute()
    {
        return data_get($this->profile,'hcpProfile.specialty');
    }

    /**
     * Brand of Nomination
     * @key brand_label
     *
     * @return string
     */
    public function getBrandLabelAttribute()
    {
        return $this->nomination->brand_label;
    }

    /**
     * Get Nomination Classification
     * @key classifications
     *
     * @return string
     */
    public function getClassificationsAttribute()
    {
        return $this->nomination->active_scgs->implode('label', ', ');
    }

    /**
     * Get Institutions
     * @key institution
     *
     * @return string
     */
    public function getInstitutionAttribute()
    {
        return data_get($this->profile,'primary_experiences.institution');
    }

    /**
     * Get line_1 from primary address
     * @key line_1
     *
     * @return string
     */
    public function getLine1Attribute()
    {
        return data_get($this->profile,'preferredAddress.line_1');
    }

    /**
     * Get line_2 from primary address
     * @key line_2
     *
     * @return string
     */
    public function getLine2Attribute()
    {
        return data_get($this->profile,'preferredAddress.line_2');
    }

    /**
     * Get city from primary address
     * @key city
     *
     * @return string
     */
    public function getCityAttribute()
    {
        return data_get($this->profile,'preferredAddress.city');
    }

    /**
     * Get state from primary address
     * @key state_province
     *
     * @return string
     */
    public function getStateProvinceAttribute()
    {
        return data_get($this->profile,'preferredAddress.state_province');
    }

    /**
     * Get postal code from primary address
     * @key postal_code
     *
     * @return string
     */
    public function getPostalCodeAttribute()
    {
        return data_get($this->profile,'preferredAddress.postal_code');
    }

    /**
     * Get email of speaker
     * @key primary_email
     *
     * @return string
     */
    public function getPrimaryEmailAttribute()
    {
        return data_get($this->profile,'speakerProfile.primary_email');
    }

    /**
     * Get phone of speaker
     * @key primary_phone
     *
     * @return string
     */
    public function getPrimaryPhoneAttribute()
    {
        return data_get($this->profile,'speakerProfile.primary_phone');
    }

    /**
     * Get fax of speaker
     * @key fax
     *
     * @return string
     */
    public function getFaxAttribute()
    {
        return data_get($this->profile,'preferredAddress.fax');
    }

    /**
     * Get owner of nomination
     * @hidden owner
     *
     * @return string
     */
    public function getOwnerAttribute()
    {
        return $this->nomination->owner;
    }

    /**
     * Get territory id of rep
     * @key rep_territory_id
     *
     * @return string | null
     */
    public function getRepTerritoryIdAttribute()
    {
        return data_get($this->owner,'primary_territory.account_territory_id');
    }

    /**
     * Get department of rep
     * @key rep_department
     *
     * @return string | null
     */
    public function getRepDepartmentAttribute()
    {
        return data_get($this->owner,'primary_territory.label');
    }

    /**
     * Get name of rep
     * @key rep_name
     *
     * @return string | null
     */
    public function getRepNameAttribute()
    {
        return data_get($this->owner, 'preferred_name');
    }

    /**
     * Get email of rep
     * @key rep_email
     *
     * @return string | null
     */
    public function getRepEmailAttribute()
    {
        return data_get($this->owner, 'email');
    }

    /**
     * Get parent of rep which is manager
     * @hidden manager
     *
     * @return Profile | null
     */
    public function getManagerAttribute()
    {
        return data_get($this->owner, 'parent');
    }

    /**
     * Get territory id of manager
     * @key manager_territory_id
     *
     * @return string | null
     */
    public function getManagerTerritoryIdAttribute()
    {
        return data_get($this->manager,'primary_territory.account_territory_id');
    }

    /**
     * Get name of manager
     * @key manager_name
     *
     * @return string
     */
    public function getManagerNameAttribute()
    {
        return data_get($this->manager,'preferred_name');
    }

    /**
     * Get email of manager
     * @key manager_email
     *
     * @return string
     */
    public function getManagerEmailAttribute()
    {
        return data_get($this->manager,'email');
    }

    /**
     * Get name of manager's parent
     * @key manager_parent_name
     *
     * @return string
     */
    public function getManagerParentNameAttribute()
    {
        return data_get($this->manager,'parent.preferred_name');
    }

    /**
     * Get email of manager's parent
     * @key manager_parent_email
     *
     * @return string
     */
    public function getManagerParentEmailAttribute()
    {
        return data_get($this->manager,'parent.preferred_email');
    }

    /**
     * Get status of nomination
     * @key status_label
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->nomination->status_label;
    }

    /**
     * Get Nomination expiration date
     * @key expired_at
     *
     * @return float | null
     */
    public function getExpiredAtAttribute()
    {
        return excel_date($this->nomination->valid_to);
    }

    /**
     * Get Nomination background completion date
     * @key background_completed_at
     *
     * @return float | null
     */
    public function getBackgroundCompletedAtAttribute()
    {
        return excel_date(data_get($this->nomination, 'background.completed_at'));
    }

    /**
     * Get Nomination's Latest Contract
     * @hidden latest_contract
     *
     * @return Contract | null
     */
    public function getLatestContractAttribute()
    {
        return $this->nomination->latest_contract;
    }

    /**
     * Get contract status of Nomination
     * @key contract_status
     *
     * @return string
     */
    public function getContractStatusAttribute()
    {
        return data_get($this->latest_contract, 'status_label');
    }

    /**
     * Get contract creation date of Nomination
     * @key contract_created_at
     *
     * @return float
     */
    public function getContractCreatedAtAttribute()
    {
        return excel_date(data_get($this->latest_contract,'created_at'));
    }

    /**
     * Get contract valid from date
     * @key contract_valid_from
     *
     * @return float | null
     */
    public function getContractValidFromAttribute()
    {
        return excel_date(data_get($this->latest_contract,'valid_from'));
    }

    /**
     * Get contract valid to date
     * @key contract_valid_to
     *
     * @return float | null
     */
    public function getContractValidToAttribute()
    {
        return excel_date(data_get($this->latest_contract,'valid_to'));
    }

    /**
     * Get w9 creation date of Nomination
     * @key w9_created_at
     *
     * @return float | null
     */
    public function getW9CreatedAtAttribute()
    {
        return excel_date(data_get($this->nomination,'W9.created_at'));
    }

    /**
     * Get status of w9
     * @key w9_status
     *
     * @return string
     */
    public function getW9StatusAttribute()
    {
        return data_get($this->nomination,'W9.status_label');
    }

    /**
     * Get signing date of w9
     * @key w9_signed_at
     *
     * @return float | null
     */
    public function getW9SignedAtAttribute()
    {
        return excel_date(data_get($this->nomination,'W9.signed_w9.created_at'));
    }

    /**
     * compliance training competion date
     * @key compliance_training_completed_at
     *
     * @return float | null
     */
    public function getComplianceTrainingCompletedAtAttribute()
    {
        return excel_date(data_get($this->nomination,'compliance_training.completed_at'));
    }

    /**
     * Get speaker dial in phone number
     * @key speaker_dial_in_phone_number
     *
     * @return string
     */
    public function getSpeakerDialInPhoneNumberAttribute()
    {
        return data_get($this->profile,'dial_in_phone_number');
    }

    /**
     * Get speaker dial in host code
     * @key speaker_dial_in_host_code
     *
     * @return string
     */
    public function getSpeakerDialInHostCodeAttribute()
    {
        return data_get($this->profile,'dial_in_host_code');
    }

    /**
     * Get speaker dial in attendee code
     * @key speaker_dial_in_attendee_code
     *
     * @return string
     */
    public function getSpeakerDialInAttendeeCodeAttribute()
    {
        return data_get($this->profile,'dial_in_attendee_code');
    }

    /**
     * Get speaker's travel distance
     * @key speaker_travel_distance
     *
     * @return string | null
     */
    public function getSpeakerTravelDistanceAttribute()
    {
        return $this->implode(data_get($this->speaker_profile, 'preference_distance_travel'));
    }

    /**
     * Is speaker willing to fly
     * @key speaker_willing_to_fly
     *
     * @return boolean
     */
    public function getSpeakerWillingToFlyAttribute()
    {
        return data_get($this->speaker_profile, 'preference_willing_to_fly');
    }

    /**
     * Get speaker's program types
     * @key speaker_program_types
     *
     * @return string
     */
    public function getSpeakerProgramTypesAttribute()
    {
        return $this->implode(data_get($this->speaker_profile, 'preference_program_types'));
    }

    /**
     * Get SpeakerProfile
     *
     * @return SpeakerProfile : null
     */
    public function getSpeakerProfileAttribute()
    {
        return data_get($this->profile, 'speakerProfile');
    }

    /**
     * Get speaker's days of week
     * @key speaker_days_of_week
     * @return string
     */
    public function getSpeakerDaysOfWeekAttribute()
    {
        return $this->implode(data_get($this->speaker_profile, 'preference_days_of_week'));
    }

    /**
     * Get blocked days in nomination
     * @key blocked_days
     *
     * @return string
     */
    public function getBlockedDaysAttribute()
    {
        return $this->nomination->blocked_days->implode('from_to_label', ', ');
    }

    /**
     * Display Max Honorarium Attribute
     *
     * @return float|null   Sum of all Max Caps in the brands
     */
    public function getMaxHonoAttribute()
    {
        if($contract = $this->latest_contract){
            return $contract->maxCaps->sum('honorarium_limit');
        }

        return null;
    }

    /**
     * Get tier information in nomination
     * @key tier
     *
     * @return string
     */
    public function getTierAttribute()
    {
        return $this->nomination->tier_label;
    }

    /**
     * Implode the value
     *
     * @param  mixed $value
     * @param  string $separator
     * @param  null $default
     * @return string
     */
    protected function implode($value, $separator=', ', $default = null)
    {
        return empty($value) ? $default : implode($separator, $value);
    }
}
