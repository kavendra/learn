<?php

namespace Betta\Services\Generator\Streams\Programs\Caterer\FlsCardAuthorization;

use Carbon\Carbon;
use Betta\Models\ProgramCaterer;
use Betta\Foundation\Handlers\AbstractTransformer;

class MergeData extends AbstractTransformer
{
    /**
     * Merge Values
     *
     * @var Array
     */
    protected $keys = [
        'current_date'  ,
        'contact_name',
        'representative_name',
        'location_name',
        'location_address',
        'city_state_zip',
        'meeting_type',
        'meeting_date',
        'program_manager_name',
    ];

    /**
     * Helper values that should not be visible in resulting array
     *
     * @var array
     */
    protected $hidden = [
        'pm',
        'program',
        'field',
    ];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramCaterer
     */
    protected $caterer;

    /**
     * Create new class
     *
     * @param ProgramCaterer $caterer
     */
    public function __construct(ProgramCaterer $caterer)
    {
        $this->caterer = $caterer;
    }

    /**
     * Get the Program
     *
     * @access hidden
     * @return Betta\Models\Program | null
     */
    public function getProgramAttribute()
    {
        return data_get($this->caterer, 'program');
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
     * Get the current_date
     *
     * @return string
     */
    public function getCurrentDateAttribute()
    {
        return Carbon::now()->format('F j, Y');
    }

    /**
     * Get the Location Contact Name
     *
     * @return string
     */
    public function getContactNameAttribute()
    {
        return $this->caterer->contact_name ?: $this->caterer->name;
    }

    /**
     * Get the Location Name
     *
     * @return string
     */
    public function getLocationNameAttribute()
    {
        return data_get($this->caterer, 'name');
    }

    /**
     * Get the Location Address
     *
     * @return string
     */
    public function getLocationAddressAttribute()
    {
        return data_get($this->caterer, 'address.address_line');
    }

    /**
     * Get the City, State and Zip
     *
     * @return string
     */
    public function getCityStateZipAttribute()
    {
        return data_get($this->caterer, 'address.city_state_zip');
    }

    /**
     * Get the Meeting Type
     *
     * @return string
     */
    public function getMeetingTypeAttribute()
    {
        return data_get($this->program, 'program_type_label');
    }

    /**
     * Get the Meeting Date
     *
     * @return string
     */
    public function getMeetingDateAttribute()
    {
        return data_get($this->program, 'full_date');
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
     * Get the representative_name
     *
     * @return string
     */
    public function getRepresentativeNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }
}
