<?php

namespace Betta\Services\Generator\Streams\Conference\Chase\Handlers;

use Betta\Models\Conference;
use Betta\Helpers\DateFormats;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class PendingApprovalRow extends AbstratRowHandler
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
        'Exhibitor Start Date',
        'Exhibitor End Date',
        'Conference Name',
        'Brands',
        'Representative',
        'Status',
        'Exhibitor Fee',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [];

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
     * Conference Name
     *
     * @return string
     */
    protected function getConferenceNameAttribute()
    {
        return data_get($this->conference, 'label');
    }

    /**
     * Conference Brands
     *
     * @return string
     */
    protected function getBrandsAttribute()
    {
        return $this->conference->brands->implode('label', ', ');
    }

    /**
     * Conference rep
     *
     * @return string
     */
    protected function getRepresentativeAttribute()
    {
        return data_get($this->conference, 'primary_rep.preferred_name');
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

    /**
     * Conference Exhibitor Fee
     *
     * @return string
     */
    protected function getExhibitorFeeAttribute()
    {
        return data_get($this->conference, 'exhibitor_fee');
    }
}
