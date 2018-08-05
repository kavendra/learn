<?php

namespace Betta\Services\Generator\Streams\Usage\FieldPortal;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\LoginHistory;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{
    /**
     * Bind implementation
     *
     * @var Betta\Models\LoginHistory
     */
    protected $loginHistory;

    /**
     * Bind implementation
     *
     * @var Maatwebsite\Excel\Excel
    */
    protected $excel;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Field Portal Usage Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information about usage of field portal.';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $relations = array(
        'simulant',
        'profile.user',
    );

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'E' => self::AS_DATE,
    ];

    /**
     * Create new class instance
     *
     * @param Excel        $excel
     * @param LoginHistory $loginHistory
     */
    public function __construct(Excel $excel, LoginHistory $loginHistory)
    {
        $this->excel   = $excel;
        $this->loginHistory = $loginHistory;
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
            $excel->sheet('Field Portal Usage', function($tab){
                $tab->setColumnFormat($this->getFormats())
                    ->fromArray($this->candidates->toArray())
                    ->setAutoFilter()
                    ->setAutoSize(true)
                    ->freezeFirstRow();
                });
            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
          })
        ->store( 'xlsx', $this->getReportPath(), true );
    }

    /**
     * Load the data from the database
     *
     * @param  array $arguments
     * @return Illuminate\Support\Collection
     */
    protected function loadMergeData( $arguments )
    {
        return $this->loginHistory
                    ->byFields()
                    ->betweenDates($this->from(), $this->to())->with($this->relations)->get()
                    ->transform(function($loginHistory){
                        return (new Handlers\RowHandler($loginHistory))->fill();
                    });
    }

    /**
     * From argument
     *
     * @return Carbon\Carbon
     */
    protected function from()
    {
        return Carbon::parse(data_get($this->arguments, 'from'));
    }

    /**
     * To argument
     *
     * @return Carbon\Carbon
     */
    protected function to()
    {
        return Carbon::parse(data_get($this->arguments, 'to'))->endOfDay();
    }
}
