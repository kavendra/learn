<?php

namespace Betta\Services\Generator\Streams\Programs\Report\AttendeeHcp;

use Maatwebsite\Excel\Excel;
use Betta\Models\Program;
use Betta\Models\RegistrationStatus;
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
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Attendee Report HCP only';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about HCSP Attendees(HCP only)';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

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
     * Limit by status
     *
     * @var array
     */
    protected $status = [
        RegistrationStatus::ATTENDED,
        RegistrationStatus::ONSITE,
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

        })->store('xlsx', $this->getReportPath(), true);
    }

    /**
     * Return merge data for the report
     *
     * @todo   Clean up the accessor once the Master grid is bumped to the next version
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');

        return $this->program
                    # In case we are asked to only include the Speaker Programs
                    #->speakerPrograms()
                    # Pending Master Grid update
                    #->anyReport($arguments)
                    ->byBrand( $inBrand )
                    ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
                    ->with( $this->relations )
                    ->with(['registrations'=>function($registration){
                        $registration->hcps()->inStatus($this->status);
                    }])
                    ->noTest()
                    ->get()
                    ->pluck('registrations')
                    ->collapse()
                    ->transform(function($registration){
                        return (new Handlers\RowHandler($registration))->fill();
                    });

    }
}
