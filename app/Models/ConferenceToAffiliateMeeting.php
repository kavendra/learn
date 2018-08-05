<?php

namespace App\Models;

use Carbon\Carbon;
use Betta\Foundation\Eloquent\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Collective\Html\Eloquent\FormAccessible;

class ConferenceToAffiliateMeeting extends AbstractModel
{
    use SoftDeletes;
    use FormAccessible;
    use Traits\CreatedByTrait;

    protected $table = 'conference_to_affiliate_meeting';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conference_id',
        'meeting_name',
        'event_start_date',
        'event_start_time',
        'event_end_time',
        'estimated_attendance',
        'audience',
        'event_type',
        'event_type_other',
        'food_beverage',
        'food_beverage_other',
        'event_setup',
        'event_setup_other',
        'av_equipments',
        'additional_equipment',
        'additional',
        'name',
        'title',
        'email',
        'phone_number',
        'name_on_card',
        'card_type',
        'card_number',
        'cvv',
        'expiry_month',
        'expiry_year',
        'hotel_id',
        'hotel_meeting_room',
        'hotel_contact_name',
        'hotel_contact_phone',
        'hotel_contact_email',
        'status',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'event_start_date',
    ];

    /**
     * Manually register Event Listener
     *
     * @return Void
     */
    public static function boot()
    {
        parent::boot();

        # When creating
        static::creating(function($model)
        {
           $model->card_number = empty($model->card_number)?null:encrypt($model->card_number);
           $model->cvv = empty($model->cvv)?null:encrypt($model->cvv);
        });

        # When creating
        static::updating(function($model)
        {
            if($model->isDirty('card_number')){
                $model->card_number = empty($model->card_number)?null:encrypt($model->card_number);
            }
            if($model->isDirty('cvv')){
                $model->cvv = empty($model->cvv)?null:encrypt($model->cvv);
            }
        });
    }

     /**
     *  Conference exists in Status
     *
     * @return Relation
     */
    public function conferenceinfo()
    {
        return $this->belongsTo(Conference::class, 'conference_id');
    }


    /**
     * Get Group Name
     *
     * @return Relation
     */
    public function groupName()
    {
        return $this->belongsTo(ProfileGroup::class, 'audience');
    }

    /**
     * Meeting Event
     *
     * @return Relation
     */
    public function meetingEvent()
    {
        return $this->belongsTo(MeetingEvent::class, 'event_setup');
    }

    /**
     * Get Group Name
     *
     * @return Relation
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

     /**
     *  Affiliate Meeting History
     *
     * @return Relation
     */
    public function affiliateMeetingHistory()
    {
        return $this->hasMany(ConferenceAffiliateHistory::class, 'affiliate_meeting_id');
    }

    /**
     * Mutate the valid_from for the Form submission
     *
     * @param  string  $value
     * @return string
     */
    public function formEventStartDateAttribute($value)
    {
        return empty($value) ? '' : Carbon::parse($value)->format('m/d/Y');
    }

    /**
     * Mutate the Event Start Time for the Form submission
     *
     * @param  string  $value
     * @return string
     */
    public function formEventStartTimeAttribute($value)
    {
        return empty($value) ? '' : Carbon::parse($value)->format('g:i A');
    }

    /**
     * Mutate the Event End Time for the Form submission
     *
     * @param  string  $value
     * @return string
     */
    public function formEventEndTimeAttribute($value)
    {
        return empty($value) ? '' : Carbon::parse($value)->format('g:i A');
    }

    /**
     * Get hidden credit card attribute
     *
     * @return string
     */
    public function getHiddenCardNumberAttribute()
    {
        return str_repeat('X', 12).substr(decrypt($this->card_number), -4);
    }

    /**
     * Get hidden credit card CVV attribute
     *
     * @return string
     */
    public function getHiddenCardCvvNumberAttribute()
    {
        return str_repeat('X',strlen(decrypt($this->cvv)));

    }

     /**
     * Mutate the event_start_date
     *
     * @return string
     */
    public function getEventStartedDateAttribute()
    {
        return Carbon::parse($this->event_start_date)->format('Y-m-d');
    }


    /**
     * Resolve EventStartDate as UTC
     *
     * @return string
     */
    public function getEventStartDateUtcAttribute()
    {
        return $this->makeUtcDate($this->event_start_date);
    }


    /**
     * Resolve EventStartDate as UTC
     *
     * @return string
     */
    public function getEventStartTimeUtcAttribute()
    {
        return $this->makeUtcDate($this->event_start_time);
    }

    /**
     * Resolve EventEndDate as UTC
     *
     * @return string
     */
    public function getEventEndTimeUtcAttribute()
    {
        return $this->makeUtcDate($this->event_end_time);
    }

    /**
     * Make Any date as UTC
     *
     * @return string
     */
    protected function makeUtcDate($date)
    {
        //$timezone = object_get($this->conference->timezone, 'label_iso');

        return Carbon::create($date->year, $date->month, $date->day, $date->hour, $date->minute, 0)->timezone('UTC');
    }

    /**
     * Is requested meeting
     *
     * @return string
     */
    public function getIsRequestedAttribute()
    {
        return $this->status==ConferenceToAffiliateMeetingInterface::REQUESTED;
    }

    /**
     * Is requested meeting
     *
     * @return string
     */
    public function getIsConfirmedAttribute()
    {
        return $this->status==ConferenceToAffiliateMeetingInterface::CONFIRMED;
    }

    /**
     * Is requested meeting
     *
     * @return string
     */
    public function getIsPendingAttribute()
    {
        return $this->status==ConferenceToAffiliateMeetingInterface::PENDING;
    }

    /**
     * Is requested meeting
     *
     * @return string
     */
    public function getIsCancelledAttribute()
    {
        return $this->status==ConferenceToAffiliateMeetingInterface::CANCELLED;
    }

     /**
     * Verbal completed representation
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
       if($this->status == 1)
        $status = 'Registered';
       elseif($this->status == 2)
        $status = 'Confirmed';
       elseif($this->status == 3)
        $status = 'Pending';
       elseif($this->status == 4)
        $status = 'Canceled';
        return $status;
    }
}
