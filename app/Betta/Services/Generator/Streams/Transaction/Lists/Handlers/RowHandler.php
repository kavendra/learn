<?php

namespace Betta\Services\Generator\Streams\Transaction\Lists\Handlers;

use App\Models\ConferenceToAffiliateMeeting;
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

}
