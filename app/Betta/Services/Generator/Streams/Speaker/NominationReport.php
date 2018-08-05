<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Nomination;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class NominationReport extends AbstractReport
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
    protected $nomination;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Nomination Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker Nomination and their information';


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
        'brand',
        'owner',
        'profile.hcpProfile',
        'profile.experiences',
        'profile.addresses',
        'profile.primaryAddress',
        'profile.userProfile',
        'profile.repProfile',
        'profile.speakerProfile',
    ];


    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Nomination $nomination
     * @return Void
     */
    public function __construct(Excel $excel, Nomination $nomination)
    {
        $this->excel      = $excel;
        $this->nomination = $nomination;
    }


    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Speaker Nomination Report', function ($sheet) {
                        $sheet->loadView('reports.speaker.nomination.report')
                              ->with('nominations',  $this->candidates )
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
        return $this->nomination->with($this->relations)
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
            # Valid Dates
            'P:Q' =>  static::AS_DATE,
            'S'   =>  static::AS_DATE,
            'M'   =>  static::AS_ZIP_CODE,
        ];
    }
}
