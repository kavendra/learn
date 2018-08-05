<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary;

use Carbon\Carbon;
use Betta\Services\Generator\Foundation\BettaReport;

class Report extends BettaReport
{
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Cost Summary Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Summarize weekly costs';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * List tab handlers
     *
     * @var array
     */
    protected $tabs = [
        'programs' => Tabs\Programs\Tab::class,
        'taes' => Tabs\Taes\Tab::class,
        'costs' => Tabs\Costs\Tab::class,
        'nonProgramRelatedCosts' => Tabs\NonProgramRelatedCosts\Tab::class,
    ];
}
