<?php

namespace Betta\Services\Generator\Streams\Grids\Cost;

use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
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
    protected $title = 'Cost Grid Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'All Program Costs in one report';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'E' => self::AS_PERCENTAGE,
        'G' => self::AS_DATE,
        'H' => self::AS_TIME,
        'T:U' => self::AS_NICE_INTEGER,
        'AA' => self::AS_ZIP_CODE,
        'AB' => self::AS_PHONE,
        'AI:BM' => self::AS_CURRENCY,
        'BO' => self::AS_CURRENCY,
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'av',
        'pms',
        'costs',
        'budgetJars',
        'presentations',
        'audienceTypes',
        'registrations',
        'speakerBureau',
        'brands.programTypes',
        'programLocations.vendor',
        'programSpeakers.profile.hcpProfile',
        'fields.territories.parent.parent.primaryProfiles',
        'fields.territories.parent.primaryProfiles',
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
            $excel->sheet('Cost Grid', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->fromArray($this->candidates)
                      ->setAutoFilter()
                      ->freezeFirstRow();
                #->loadView('reports.grids.cost.report')
                #->with('programs',  $this->candidates )
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
                    ->anyReport( $this->arguments )
                    ->with($this->relations)
                    ->noTest()
                    ->get()
                    ->transform(function($program){
                        return $program->brands->transform(function($brand) use($program){
                            return (new Handlers\CostGridReportHandler($program, $brand))->fill();
                        });
                    })
                    # Program will be replaced with collection of CostGridReportHandler's
                    # And those need to
                    ->collapse()
                    ->toArray();
    }
}
