<?php

namespace Betta\Services\Generator\Streams\Speaker\ThankYou;

use Carbon\Carbon;
use Betta\Models\ProgramSpeaker;
use Betta\Foundation\Handlers\AbstractTransformer;

class MergeData extends AbstractTransformer
{
    /**
     * Merge Values
     *
     * @var Array
     */
    protected $keys = [
        'approval_code',
        'contact_email',
        'contact_number',
        'current_date',
        'location_city',
        'location_state',
        'pm_fax',
        'program_full_date',
        'program_full_start_time',
        'program_id',
        'program_location',
        'program_manager_email',
        'program_manager_name',
        'program_presentation',
        'program_representative',
        'program_speaker_brand',
        'program_time_full',
        'program_type',
        'speaker_address',
        'speaker_city',
        'speaker_full_name_degree',
        'speaker_state',
        'speaker_zip',
        'speakers_degrees',
    ];

    /**
     * Helper values that should not be visible in resulting array
     *
     * @var array
     */
    protected $hidden = [
        'pm',
        'field',
        'address',
        'profile',
        'program',
        'location',
        'speakers',
    ];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

    /**
     * Create new class
     *
     * @param ProgramSpeaker $programSpeaker
     */
    public function __construct(ProgramSpeaker $programSpeaker)
    {
        $this->programSpeaker = $programSpeaker;
    }

    /**
     * Get the Program Manager
     *
     * @access hidden
     * @return Betta\Models\Profile | null
     */
    public function getPmAttribute()
    {
        return data_get($this->program, 'primary_pm');
    }

    /**
     * Get the Field
     *
     * @access hidden
     * @return Betta\Models\Profile | null
     */
    public function getFieldAttribute()
    {
        return data_get($this->program, 'primary_field');
    }

    /**
     * Get the Speaker's Address
     *
     * @access hidden
     * @return Betta\Models\Address | null
     */
    public function getAddressAttribute()
    {
        return data_get($this->profile, 'preferred_address');
    }

    /**
     * Get the Speaker's Profile
     *
     * @access hidden
     * @return Betta\Models\Profile | null
     */
    public function getProfileAttribute()
    {
        return data_get($this->programSpeaker, 'profile');
    }

    /**
     * Get the Program
     *
     * @access hidden
     * @return Betta\Models\Program | null
     */
    public function getProgramAttribute()
    {
        return data_get($this->programSpeaker, 'program');
    }

    /**
     * Get the Location
     *
     * @access hidden
     * @return Betta\Models\ProgramLocation | null
     */
    public function getLocationAttribute()
    {
        return data_get($this->program, 'primaryLocation');
    }

    /**
     * Get the Speakers
     *
     * @access hidden
     * @return Illuminat\support\Collection
     */
    public function getSpeakersAttribute()
    {
        return data_get($this->program, 'primarySpeakers', collect([]));
    }

    /**
     * Get the approval_code
     *
     * @return string
     */
    public function getApprovalCodeAttribute()
    {
        return '';
    }

    /**
     * Get the contact_email
     *
     * @return string
     */
    public function getContactEmailAttribute()
    {
        return data_get($this->pm, 'userProfile.email')  ?: config('fls.support_email');
    }

    /**
     * Get the contact_number
     *
     * @return string
     */
    public function getContactNumberAttribute()
    {
        return config('fls.support_phone');
    }

    /**
     * Get the current_date
     *
     * @return string
     */
    public function getCurrentDateAttribute()
    {
        return Carbon::now()->format('F j, Y');
    }

    /**
     * Get the location_city
     *
     * @return string
     */
    public function getlocationCityAttribute()
    {
        return data_get($this->location, 'address.city');
    }

    /**
     * Get the Program Location:: State
     *
     * @return string
     */
    public function getlocationStateAttribute()
    {
        return data_get($this->location, 'address.state_province');
    }

    /**
     * Get the PM Fax
     *
     * @return string
     */
    public function getPmFaxAttribute()
    {
        return config('fls.support_fax');
    }

    /**
     * Get the program_full_date
     *
     * @return string
     */
    public function getProgramFullDateAttribute()
    {
        return data_get($this->program, 'full_start_date');
    }

    /**
     * Get the program_full_start_time
     *
     * @return string
     */
    public function getProgramFullStartTimeAttribute()
    {
        return data_get($this->program, 'full_start_time');
    }
    /**
     * Get the program_id
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return data_get($this->program, 'id');
    }
    /**
     * Get the program_location
     *
     * @return string
     */
    public function getProgramLocationAttribute()
    {
        return data_get($this->location, 'name');
    }
    /**
     * Get the program_manager_email
     *
     * @return string
     */
    public function getProgramManagerEmailAttribute()
    {
        return data_get($this->pm, 'userProfile.email')  ?: config('fls.support_email');
    }

    /**
     * Get the program_manager_name
     *
     * @return string
     */
    public function getProgramManagerNameAttribute()
    {
        return data_get($this->pm, 'preferred_name');
    }
    /**
     * Get the program_presentation
     *
     * @return string
     */
    public function getProgramPresentationAttribute()
    {
        return data_get($this->program, 'title');
    }

    /**
     * Get the program_representative
     *
     * @return string
     */
    public function getProgramRepresentativeAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Get the Primary Brand of the speaker
     *
     * @return string
     */
    public function getProgramSpeakerBrandAttribute()
    {
        return data_get($this->program, 'primaryBrand.label');
    }

    /**
     * Get the Full Program Time
     *
     * @return string
     */
    public function getProgramTimeFullAttribute()
    {
        return data_get($this->program, 'full_start_time');
    }

    /**
     * Get the Program Type Label
     *
     * @return string
     */
    public function getProgramTypeAttribute()
    {
        return data_get($this->program, 'program_type_label');
    }

    /**
     * Get the Speaker Address: nice Line
     *
     * @return string
     */
    public function getSpeakerAddressAttribute()
    {
        return data_get($this->address, 'address_line');
    }

    /**
     * Get the Speaker Address: City
     *
     * @return string
     */
    public function getSpeakerCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Get the Speaker Full Name and Degree
     *
     * @return string
     */
    public function getSpeakerFullNameDegreeAttribute()
    {
        return $this->programSpeaker->preferred_name_degree;
    }

    /**
     * Get the Speaker Address:  State
     *
     * @return string
     */
    public function getSpeakerStateAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Get the Speaker Address: Postal Code
     *
     * @return string
     */
    public function getSpeakerZipAttribute()
    {
        return data_get($this->address, 'postal_code');
    }

    /**
     * Get the Speakers and Degrees
     *
     * @return string
     */
    public function getSpeakersDegreesAttribute()
    {
        return $this->speakers->implode('preferred_name_degree', ', ');
    }

}
