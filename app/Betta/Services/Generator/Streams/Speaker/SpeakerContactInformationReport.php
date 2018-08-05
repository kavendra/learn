<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Profile;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class SpeakerContactInformationReport extends AbstractReport
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
    protected $title = 'Speaker Contact Information Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker information';


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
        'user',
        'brands',
        'addresses',
        'assistants',
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
     * @return Mixed
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Speaker Contact Information', function ($sheet) {
                        $sheet->loadView('reports.speaker.speaker-contact-information.report')
                              ->with('profiles',  $this->candidates )
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

		$inBrand = array_get($arguments, 'inBrand' );
		$inBrand = (is_array($inBrand)) ? $inBrand : $inBrand->pluck('id');

        return $this->profile
                    ->speakers()
                    ->whereHas('nominations.brand', function($brand) use ($inBrand){
                        $brand->byKey($inBrand);
                    })
                    ->with($this->relations)
                    ->orderBy('last_name')
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
            'O' => self::AS_ZIP_CODE,
            'R:W' => self::AS_PHONE,
        ];
    }
}
