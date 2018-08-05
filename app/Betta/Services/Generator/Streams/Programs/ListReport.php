<?php
namespace Betta\Services\Generator\Streams\Programs;

use Carbon\Carbon;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class ListReport extends AbstractReport
{

    /**
     * Bind the implementation
     *
     * @var
     */
    protected $excel;


    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $program;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Program List Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Program information';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;


    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
                'brands.programTypes',
                'programType',
                'programStatus',
                'programSpeakers.profile',
                'fields',
            ];


    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Program $program)
    {
        $this->excel   = $excel;
        $this->program = $program;
    }


    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Program List Report', function ($sheet) {
                        $sheet->loadView('reports.program.list.report')
                              ->with('programs',  $this->candidates )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    $this->includeSqlTab($excel);

                    # Make the first sheet active
                    $excel->setActiveSheetIndex(0);

                })->store('xlsx', $this->getReportPath(), true);
    }


    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');

        $filterFrom = array_get($arguments , 'from', $this->getDefaultFrom() );
        $filterTo = array_get($arguments , 'to', $this->getDefaultTo() );

        return $this->program
                    ->byBrand($inBrand)
                    ->betweenDates( $filterFrom, $filterTo )
                    ->with($this->relations)
                    ->get();
    }


    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return [ 'B'     => static::AS_DATE,];
    }

    protected function getDefaultFrom()
    {
        return Carbon::parse('January 1')->format('Y-m-d');
    }

    /**
     * Default To Date
     *
     * @return string
     */
    protected function getDefaultTo()
    {
        return Carbon::parse('December 31')->format('Y-m-d');
    }
}
