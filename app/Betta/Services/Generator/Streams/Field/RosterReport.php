<?php

namespace Betta\Services\Generator\Streams\Field;

use Betta\Models\Alignment;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class RosterReport extends AbstractReport
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
     * @var Betta\Models\Alignment
     */
    protected $alignment;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Field Roster Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about Field Roster';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'brands',
        'territories.profiles.brands',
        'territories.profiles.groups',
        'territories.profiles.repProfile',
        'territories.profiles.userProfile',
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Alignment $alignment
     * @return Void
     */
    public function __construct(Excel $excel, Alignment $alignment)
    {
        $this->excel   = $excel;
        $this->alignment = $alignment;
    }


    /**
     * Produce the report
     *
     * @return Excel instance
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
                    # Set standard properties on the file
                    $this->setProperties($excel);
                    # Produce the tab
                    $excel->sheet('Roster ', function ($sheet) {
                        $sheet->loadView('reports.field.roster.report')
                              ->with('alignments',  $this->candidates )
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
        return $this->alignment
                    ->with( $this->relations )
                    ->noTest()
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
        return [];
    }
}
