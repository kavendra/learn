<?php

namespace Betta\Services\Generator\Streams\Programs\Report\ManagementFee;

use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class ManagementFeeReport extends AbstractReport
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
    protected $title = 'Management Fee Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about Management Fees';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Format the Report
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var Array
     */
    protected $formats = [
        'B' => self::AS_DATE,
        'L' => self::AS_DATE,
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'pms',
        'costs',
        'brands.programTypes',
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

    /**
     * Produce the Report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            $excel->sheet($this->title, function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->fromArray($this->candidates)
                      ->setAutoFilter()
                      ->freezeFirstRow();
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
        return $this->program
                    ->anyReport($this->arguments)
                    ->with($this->relations)
                    ->noTest()
                    ->get()
                    ->transform(function($program){
                        return (new Handlers\ProgramManagementFeeRow($program))->fill();
                    })
                    ->toArray();
    }
}
