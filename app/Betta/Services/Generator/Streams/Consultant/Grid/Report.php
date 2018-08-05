<?php

namespace Betta\Services\Generator\Streams\Consultant\Grid;

use Carbon\Carbon;
use Betta\Models\Consultant;
use Betta\Services\Generator\Foundation\BettaReport;

class Report extends BettaReport
{
    /**
     * Bind implementation
     *
     * @var Betta\Models\Consultant
     */
    protected $consultant;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Consultant Grid Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information about consultants';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * List the tabs as $tabName => Handling Class
     *
     * @var array
     */
    protected $tabs = [
        'Consultant Grid' => Tabs\GridTab::class,
    ];

    /**
     * Create new class instance
     *
     * @param LoginHistory $loginHistory
     */
    public function __construct(Consultant $consultant)
    {
        $this->consultant = $consultant;
    }

    /**
     * Create the Report
     *
     * @return Object
     */
    protected function process()
    {
        # make new template
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            #method could be repeated
            foreach($this->tabs as $name => $tab){
                $excel->sheet($name, app($tab, [$this])->render());
            }
            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
          })
        ->store('xlsx', $this->getReportPath(), true);
    }
}
