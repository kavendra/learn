<?php

namespace Betta\Services\Generator\Streams\Conference\Chase\Handlers;

use Betta\Models\Conference;
use Betta\Helpers\DateFormats;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RequestedMaterailsRow extends AbstratRowHandler
{
    /**
     * Betta\Models\Conference
     *
     * @var Illuminate\Support\Collection
     */
    protected $conference;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Conference ID',
        'Conference Name',
        'Exhibitor Start Date',
        'Exhibitor End Date',
        'Status',
    ];

    /**
     * Create new Row instance
     *
     * @param Conference $this->conference
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }

    /**
     * Conference ID Attribute for the conference
     *
     * @return int
     */
    protected function getConferenceIdAttribute()
    {
        return data_get($this->conference, 'id');
    }

    /**
     * Conference Name
     *
     * @return string
     */
    protected function getConferenceNameAttribute()
    {
        return data_get($this->conference, 'label');
    }

    /**
     * Exhibitor Start Date Attribute for the conference
     *
     * @return string
     */
    protected function getExhibitorStartDateAttribute()
    {
        return excel_date($this->conference->exibitor_start_date);
    }

    /**
     * Exhibitor End Date Attribute for the conference
     *
     * @return string
     */
    protected function getExhibitorEndDateAttribute()
    {
        return excel_date($this->conference->exibitor_end_date);
    }

    /**
     * Conference Status
     *
     * @return string
     */
    protected function getStatusAttribute()
    {
        return data_get($this->conference, 'status_label');
    }
}
