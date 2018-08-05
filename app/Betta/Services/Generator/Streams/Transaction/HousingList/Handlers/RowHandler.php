<?php

namespace Betta\Services\Generator\Streams\Conference\HousingList\Handlers;

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
        'First Name',
        'Last Name',
        'Title',
        'Department',
        'Hotel',
        'CheckIn',
        'CheckOut',
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
     protected function getDepartmentAttribute()
    {
        return data_get($this->conferenceHousing, 'departmentName.label');
    }

     /**
     * Get Hotel
     *
     * @return string | null
     */
     protected function getHotelAttribute()
    {
        return data_get($this->conferenceHousing, 'hotel.hotel_name');
    }

     /**
     * Get CheckIn
     *
     * @return string | null
     */
     protected function getCheckInAttribute()
    {
        return ($this->conferenceHousing->arrival_date) ? $this->conferenceHousing->arrival_date->format("m/d/Y") : null;
    }

     /**
     * Get CheckOut
     *
     * @return string | null
     */
     protected function getCheckOutAttribute()
    {
        return ($this->conferenceHousing->departure_date) ? $this->conferenceHousing->departure_date->format("m/d/Y") : null;
    }

    /**
     * Get Status
     *
     * @return string | null
     */
    protected function getStatusAttribute()
    {
        return data_get($this->conferenceHousing, 'status_label');
    }
}
