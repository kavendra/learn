<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Attendee;

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
    protected $title = 'Program Attendee Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about HCSP Attendees';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;

    /**
     * Set the Formats for the Report Tab
     *
     * @var array
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
        'registrations.profile',
        'registrations.profile.hcpProfile',
        'registrations.context.brands',
        'registrations.context.fields',
        'registrations.registrationStatus',
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
                      ->fromArray($this->candidates->toArray())
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });
            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
        # Produce report
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
        # Filter in brand, if provided
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');
        # start builder
        $all = $this->program->noTest()->with( $this->relations );
        # add inject
        if($id = data_get($arguments, 'id')){
            $all->ByKey($id);
        }
        # resolve builder
        return $all->byBrand( $inBrand )
                    ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
                    ->get()
                    ->pluck('registrations')
                    ->collapse()
                    ->sortBy('last_first_name')
                    ->transform(function($registration){
                        return (new Handlers\ProgramAttendeesRow($registration))->fill();
                    });
    }
}
