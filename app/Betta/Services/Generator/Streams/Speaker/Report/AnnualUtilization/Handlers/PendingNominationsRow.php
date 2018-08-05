<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\AnnualUtilization\Handlers;

use Betta\Models\Nomination;
use Betta\Models\ProgramSpeaker;
use Illuminate\Support\Collection;

class PendingNominationsRow extends ActiveNominationsRow
{
     /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Speaker Status',
        'Customer Master ID',
        'Last Name',
        'First Name',
        'NPI',
        'Total Completed/Closed Out',
        'Hono Completed/Closed Out',
        'Total Upcoming/Confirmed',
        'Hono Upcoming/Confirmed',
        'Hono Cancelled Paid',
        'Total # Programs',
        'Total Hono',
        'Last Complete',
        'Territory ID',
        'Territory Name',
        'Representative Name',
        'District ID',
        'District Name',
        'District Manager',
        'Region ID',
        'Region Name',
        'National Sales Director',
        'Max Hono',
        'Tier',
        'Pending Requirements',
        'Speaker Bureau',
    ];

    /**
     * Get the Nomination Status
     *
     * @return string
     */
    protected function getSpeakerStatusAttribute()
    {
        return $this->nomination->status_label;
    }
}
