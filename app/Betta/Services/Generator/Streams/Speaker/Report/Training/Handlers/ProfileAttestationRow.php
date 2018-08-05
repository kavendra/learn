<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Training\Handlers;

use Betta\Models\Profile;
use Betta\Models\SpeakerAttestation;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ProfileAttestationRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\SpeakerAttestation
     */
    protected $attestation;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Profile
     */
    protected $profile;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'last_name',
        'first_name',
        'customer_master_id',
        'attestation_type',
        'completed_at'
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = ['profile'];

    /**
     * Create new Row instance
     *
     * @param SpeakerAttestation $attestation
     * @param Profile $profile
     */
    public function __construct(SpeakerAttestation $attestation, Profile $profile)
    {
        $this->attestation = $attestation;
        $this->profile = $profile;
    }

    /**
     * Last Name from Profile
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return $this->profile->last_name;
    }

    /**
     * First Name from Profile
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return $this->profile->first_name;
    }

    /**
     * Customer Id from Profile
     *
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return $this->profile->customer_master_id;
    }

    /**
     * Attestation Type
     *
     * @return string
     */
    public function getAttestationTypeAttribute()
    {
        return $this->attestation->attestation_type;
    }

    /**
     * Customer Id from Profile
     *
     * @return string
     */
    public function getCompletedAtAttribute()
    {
        return excel_date($this->attestation->pivot->attestation_date);
    }
}

