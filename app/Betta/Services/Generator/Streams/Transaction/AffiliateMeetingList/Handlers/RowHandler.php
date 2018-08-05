<?php

namespace Betta\Services\Generator\Streams\Conference\AffiliateMeetingList\Handlers;

use Betta\Models\ConferenceToAffiliateMeeting;
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
        'Submit Date',
        'Meeting date',
        'Meeting start time',
        'Meeting end time',
        'Meeting estimated attendance',
        'Audience type',
        'Meeting type',
        'Meeting Setup',
        'Your Name (Requestor)',
        'Email (Requestor)',
        'Phone (requestor)',
        'Hotel',
        'Meeting room name',
        'Hotel Contact Name',
        'Hotel Contact Phone',
        'Cardholder name',
        'CC number',
        'Exp. Month',
        'Status (confirmed or not confirmed)',
    ];

    /**
     * Create new Row instance
     *
     * @param ConferenceToAffiliateMeeting $conferenceToAffiliateMeeting
     */
    public function __construct(ConferenceToAffiliateMeeting $conferenceToAffiliateMeeting)
    {
        $this->conferenceToAffiliateMeeting = $conferenceToAffiliateMeeting;
    }

    /**
     * Get Department Name
     *
     * @return string | null
     */
    protected function getIdAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'id');
    }

    /**
     * Submit Date
     *
     * @return string | null
     */
     protected function getSubmitDateAttribute()
    {
        return $this->conferenceToAffiliateMeeting->created_at->format('m/d/y');
    }

     /**
     * Meeting Date
     *
     * @return string | null
     */
     protected function getMeetingDateAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'event_start_date')->format('m/d/y');
    }

     /**
     * Meeting starting time
     *
     * @return string
     */
    protected function getMeetingStartTimeAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'event_start_time');
    }

     /**
     * Meeting end time
     *
     * @return string
     */
    protected function getMeetingEndTimeAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'event_end_time');
    }

     /**
     * Get Status
     *
     * @return string | null
     */
    protected function getMeetingEstimatedAttendanceAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'estimated_attendance');
    }

    /**
     * Audience Type
     *
     * @return string
     */
    protected function getAudienceTypeAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'groupName.label');
    }

    /**
     * Meeting type
     *
     * @return string
     */
    protected function getMeetingTypeAttribute()
    {
        return $this->conferenceToAffiliateMeeting->event_type;
    }

    /**
     * Meeting setup
     *
     * @return string
     */
    protected function getMeetingSetupAttribute()
    {
        return data_get($this->conferenceToAffiliateMeeting, 'meetingEvent.label');
    }

    /**
     * Your Name (Requestor)
     *
     * @return string
     */
    protected function getYourNameRequestorAttribute()
    {
        return $this->conferenceToAffiliateMeeting->name;
    }

    /**
     * Email (Requestor)
     *
     * @return string
     */
    protected function getEmailRequestorAttribute()
    {
        return $this->conferenceToAffiliateMeeting->email;
    }

    /**
     * Phone (Requestor)
     *
     * @return string
     */
    protected function getPhoneRequestorAttribute()
    {
        return $this->conferenceToAffiliateMeeting->phone_number;
    }

    /**
     * Hotel
     *
     * @return string
     */
    protected function getHotelAttribute()
    {
        return $this->conferenceToAffiliateMeeting->hotel_name;
    }

    /**
     * Meeting Room Name
     *
     * @return string
     */
    protected function getMeetingRoomNameAttribute()
    {
        return $this->conferenceToAffiliateMeeting->hotel_meetingroom_name;
    }

    /**
     * Hotel Contact Name
     *
     * @return string
     */
    protected function getHotelContactNameAttribute()
    {
        return $this->conferenceToAffiliateMeeting->hotel_contact_name;
    }

    /**
     * Hotel Contact Phone
     *
     * @return string
     */
    protected function getHotelContactPhoneAttribute()
    {
        return $this->conferenceToAffiliateMeeting->hotel_contact_phone;
    }

    /**
     * Cardholder name
     *
     * @return string
     */
    protected function getCardholderNameAttribute()
    {
        return $this->conferenceToAffiliateMeeting->name_on_card;
    }

    /**
     * CC Name
     *
     * @return string
     */
    protected function getCCNameAttribute()
    {
        return $this->conferenceToAffiliateMeeting->card_type;
    }

    /**
     * CC number
     *
     * @return string
     */
    protected function getCCNumberAttribute()
    {
        try{
            return $this->conferenceToAffiliateMeeting->hidden_card_number;
        } catch(\Exception $e){
            return '';
        }
    }

    /**
     * Exp. Month
     *
     * @return string
     */
    protected function getExpMonthAttribute()
    {
        if( $month = $this->conferenceToAffiliateMeeting->expiry_month){
            return now()->startOfYear()->addMonth($month -1 )->format('M');
        } else{
            return '';
        }
    }

    /**
     * Status (confirmed or not confirmed)
     *
     * @return string
     */
    protected function getStatusConfirmedOrNotConfirmedAttribute()
    {
        return $this->conferenceToAffiliateMeeting->status ? 'Confirmed' : 'Not Confirmed';
    }
}
