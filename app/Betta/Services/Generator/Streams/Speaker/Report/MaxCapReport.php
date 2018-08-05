<?php

namespace Betta\Services\Generator\Streams\Speaker\Report;

use Betta\Models\MaxCap;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class MaxCapReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\MaxCap
     */
    protected $maxCap;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Max Cap Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect Speaker Max Cap Information';

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
        'profile',
        'contracts',
        'costs',
    ];

    /**
     * List all column formats
     *
     * @var array
     */
    protected $formats = [];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  MaxCap $maxCap
     * @return Void
     */
    public function __construct(Excel $excel, MaxCap $maxCap)
    {
        $this->excel   = $excel;
        $this->maxCap = $maxCap;
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
                    $excel->sheet('Max Caps', function ($sheet) {
                        $sheet->loadView('reports.speaker.max-cap.report')
                              ->with('maxCaps',  $this->candidates )
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter()
                              ->freezeFirstRow();
                    });

                    $excel->sheet('Lilne Item Costs', function ($sheet) {
                        $sheet->loadView('reports.speaker.max-cap.costs')
                              ->with('maxCaps',  $this->candidates )
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
        $builder = $this->maxCap->with($this->relations);

        if($maxCap = data_get($arguments, 'maxCap') AND $maxCap instanceOf MaxCap){
            $builder->byKey( $maxCap->getKey() );
        } elseif ($id = data_get($arguments, 'id')) {
            $builder->byKey( $id );
        }

        return $builder->get()->sortBy('profile.last_name');
    }

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return $this->formats;
    }
}
