<?php

namespace Betta\Services\Generator\Streams\Conference\ScheduleList\Handlers;

use Betta\Models\ConferenceSchedule;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conferenceSchedule;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Meeting Date',
        'Time',
        'Host Name',
        'Host Title',
        'Host Email',
        'Host Phone',
        'Meeting Description',
        'Attendee Names',
        'Order By Date',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'conferenceSchedules',
    ];

    /**
     * Create new Row instance
     *
     * @param ConferenceSchedule $conferenceSchedule
     */
    public function __construct($collection, $room)
    {
        $this->collection = $collection;
        $this->room       = $room;
    }

    /**
     * Get Meeting Date
     *
     * @return string
     */
    protected function getMeetingDateAttribute()
    {
        return $this->collection->start_time->format("m/d/Y");
    }

    /**
     * Get Time
     *
     * @return float
     */
    protected function getTimeAttribute()
    {
        return $this->collection->start_time->format("h:i A");
    }

    /**
     * Get Room
     *
     * @return int
     */
    protected function getConferenceSchedulesAttribute()
    {
        if(isset($this->collection->schedule[$this->room->id])){
            return $this->collection->schedule[$this->room->id];
        }
        return;
    }

    /**
     * Verbose Host Name
     *
     * @return string
     */
    protected function getHostNameAttribute()
    {
        return data_get($this->conferenceSchedules, 'host_name');
    }

    /**
     * Get Host Title
     *
     * @return string
     */
     protected function getHostTitleAttribute()
    {
        return data_get($this->conferenceSchedules, 'host_title');
    }

    /**
     * Get Host Email
     *
     * @return string
     */
     protected function getHostEmailAttribute()
    {
        return data_get($this->conferenceSchedules, 'host_email');
    }

     /**
     * Get Host Phone
     *
     * @return string
     */
     protected function getHostPhoneAttribute()
    {
        return data_get($this->conferenceSchedules, 'host_mobile');
    }

     /**
     * Get Meeting Description
     *
     * @return string
     */
     protected function getMeetingDescriptionAttribute()
    {
        return data_get($this->conferenceSchedules, 'meeting_description');
    }

     /**
     * Get Attendee Names
     *
     * @return string
     */
    protected function getAttendeeNamesAttribute()
    {
        if($this->conferenceSchedules AND $this->conferenceSchedules->has('attendees')) {
        return $this->conferenceSchedules->attendees->implode('attendee_name', ', ');
        }
    }

    /**
     * Get Order By Date
     *
     * @return string
     */
     protected function getOrderByDateAttribute()
    {
        return excel_date(data_get($this->conferenceSchedules, 'created_at'));
    }
}
