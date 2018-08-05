<?php

namespace Betta\Services\Generator\Streams\Speaker\Report;

use Betta\Models\Profile;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class ProfileDetailReport extends AbstractReport
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
    protected $profile;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Profile Details Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect Speaker Information';


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
        'user',
        'brands',
        'addresses',
        'documents',
        'hcpProfile',
        'speakerProfile',
        'primaryAddress',
    ];


    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Profile $profile
     * @return Void
     */
    public function __construct(Excel $excel, Profile $profile)
    {
        $this->excel   = $excel;
        $this->profile = $profile;
    }


    /**
     * Produce the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Details', function ($sheet) {
                        $sheet->loadView('reports.speaker.profile-detail.report')
                              ->with('profiles',  $this->candidates )
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter()
                              ->freezeFirstRow();
                    });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    $this->includeSqlTab( $excel );

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
        $builder = $this->profile->speakers()
                        ->with($this->relations)
                        ->search()
                        ->orderBy('last_name');

        return $builder->get();
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
            'N' => self::AS_ZIP_CODE,
            'Q:T' => self::AS_PHONE,
            'V' => AbstractReport::AS_DATE,
        ];
    }
}
