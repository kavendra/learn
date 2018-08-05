<?php

namespace Betta\Services\Generator\Streams\Usage\BusinessReview;

use Auth;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Models\Pivots\ProgramBrandPivot;
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
    protected $title = 'Business Review Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information about LSP Team activity';

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
        'pms',
        'brands.programTypes',
        'fields.territories.profiles',
        'fields.communications',
        'pms',
        'shipments',
        'createdBy',
        'programType',
        'timezone',
        'programStatus',
        'av',
        'histories.to',
        'registrations',
        'audienceTypes',
        'presentations',
        'programInvitation',
        'programCaterers.vendor',
        'programCaterers.progressionStatus',
        'programSpeakers.profile',
        'programSpeakers.progressions',
        'programLocations.progressionStatus',
        'programLocations.progressions',
        'programLocations.address',
        'programSpeakers.travels.progressions',
        'programSpeakers.profile.hcpProfile',
    ];

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'F' => self::AS_DATE,
        'G' => self::AS_TIME,
        'AL:AM' => self::AS_DATE,
        'AO:AP' => self::AS_DATE,
        'AS:AY' => self::AS_DATE,
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
     * Process the report
     *
     * @return Object
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            $excel->sheet('Business Review', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->fromArray($this->candidates->toArray())
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
                    ->anyReport($arguments)
                    ->with($this->relations)
                    ->noTest()
                    ->get()
                    ->transform(function($program){
                        return (new Handlers\BusinessReviewRow($program))->fill();
                    });
    }
}
