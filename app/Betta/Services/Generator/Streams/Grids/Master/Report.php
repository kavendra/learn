<?php

namespace Betta\Services\Generator\Streams\Grids\Master;

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
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Master Grid Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Master Grid Report';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Column formats
     *
     * @var array
     */
    protected $formats = [
        'K'     => self::AS_DATE,
        'N'     => self::AS_DATE,
        'O'     => self::AS_TIME,
        'Q'     => self::AS_DATE,
        'R'     => self::AS_TIME,
        'AI'    => self::AS_ZIP_CODE,
        'BD'    => self::AS_CURRENCY,
        'BW:BX' => self::AS_DATE,
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'pms',
        'av.notes',
        'av.progressionStatus',
        'audienceTypes',
        'brands.programTypes',
        'brands.programTypes',
        'cancellations',
        'cancellations.notes',
        'createdBy',
        'fields.repProfile',
        'fields.territories.primaryProfiles.territories.primaryProfiles.territories',
        'histories.to',
        'presentations.topics',
        'programInvitation',
        'programCaterers.notes',
        'programLocations.notes',
        'programSpeakers.travel',
        'programSpeakers.notes',
        'programSpeakers.honoraria',
        'programSpeakers.profile.contracts',
        'programSpeakers.profile.attestations',
        'programSpeakers.profile.trainings.trainingCourse.presentations',
        'programSpeakers.profile.hcpProfile',
        'speakerBureau',
        'registrations',
        'territory',
    ];

    /**
     * Create new instance of Report
     *
     * @param  Excel   $excel
     * @param  Betta\Models\Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Program $program)
    {
        $this->excel   = $excel;
        $this->program = $program;
    }

    /**
     * Process and make the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
                    # Set standard properties on the file
                    $this->setProperties($excel);
                    # Produce the tab
                    $excel->sheet('Master Grid', function ($sheet) {
                        $sheet->setColumnFormat( $this->getFormats() )
                              ->fromArray($this->getCandidates()->toArray() )
                              ->freezeFirstRow()
                                # Make sure to use it last
                                # This is because Excel cannot always figure out that we have data. So.
                              ->setAutoFilter();
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
                    ->transform(function(Program $program){
                        # future records
                        $records = collect([]);
                        # iterate
                        foreach($program->brands as $brand){
                            $records->push( with(new Handlers\RowHandler($program, $brand))->fill() );
                        }
                        # return a collection (could be one record)
                        return $records;
                    })
                    ->collapse();
    }
}
