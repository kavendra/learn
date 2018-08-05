<?php

namespace Betta\Services\Generator\Streams\Conference\RegistrationList\Handlers;

use Betta\Models\ConferenceHousing;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'ID',
        'Registration date',
        'First Name',
        'Last Name',
        'Title',
        'Manager',
        'Department',
        'Email',
        'Phone',
        'Status',
    ];

    /**
     * Create new Row instance
     *
     * @param ConferenceHousing $conferenceHousing
     */
    public function __construct(ConferenceHousing $conferenceHousing)
    {
        $this->conferenceHousing = $conferenceHousing;
    }

     /**
     * Get Housing ID
     *
     * @return string | null
     */
     protected function getIdAttribute()
    {
        return data_get($this->conferenceHousing, 'id');
    }

    /**
     * Get Housing ID
     *
     * @return string | null
     */
     protected function getRegistrationDateAttribute()
    {
        return data_get($this->conferenceHousing, 'created_at')->format('m/d/y');
    }

    /**
     * Get First Name
     *
     * @return string | null
     */
    protected function getFirstNameAttribute()
    {
        return data_get($this->conferenceHousing, 'first_name');
    }

    /**
     * Get Last Name
     *
     * @return string | null
     */
    protected function getLastNameAttribute()
    {
        return data_get($this->conferenceHousing, 'last_name');
    }

    /**
     * Get Title
     *
     * @return string | null
     */
    protected function getTitleAttribute()
    {
        return data_get($this->conferenceHousing, 'title');
    }

     /**
     * Get Department
     *
     * @return string | null
     */
     protected function getManagerAttribute()
    {
        return data_get($this->conferenceHousing, 'manager_name');
    }

     /**
     * Get Department
     *
     * @return string | null
     */
     protected function getDepartmentAttribute()
    {
        return data_get($this->conferenceHousing, 'departmentName.label');
    }

     /**
     * Get Email
     *
     * @return string | null
     */
     protected function getEmailAttribute()
    {
        return data_get($this->conferenceHousing, 'email');
    }

     /**
     * Get Phone
     *
     * @return string | null
     */
    protected function getPhoneAttribute()
    {
        return the_phone($this->conferenceHousing->phone);
    }

    /**
     * Get State
     *
     * @return string | null
     */
    protected function getStatusAttribute()
    {
        return data_get($this->conferenceHousing, 'status_label');
    }
}
