<?php

namespace Betta\Services\Generator\Streams\Usage;

use Betta\Models\Program;
use Betta\Models\Pivots\ProgramBrandPivot;

use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;
use Auth;

class BusinessReviewReport extends AbstractReport
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
        'brands',
        'fields',
        'av.notes',
        'histories.to',
        'registrations',
        'audienceTypes',
        'presentations',
        'programInvitation',
        'programCaterers.notes',
        'programSpeakers.notes',
        'programLocations.notes',
        'programSpeakers.travel',
        'programSpeakers.honoraria',
        'programSpeakers.profile.hcpProfile',
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
		//dd($this->candidates);
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);
                    # Produce the tab
                    $excel->sheet('Business Review', function ($sheet) {
                        $sheet->loadView('reports.usage.business-review.report')
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
        return $this->program
                    ->byBrand(array_get($arguments, 'inBrand' ) )
                    //->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
                    ->with($this->relations)
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
        return [
		  'F' => self::AS_DATE,
          'AO:AV' => self::AS_DATE,
		];
    }
}
