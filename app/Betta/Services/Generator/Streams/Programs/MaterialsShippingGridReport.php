<?php

namespace Betta\Services\Generator\Streams\Programs;

use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class MaterialsShippingGridReport extends AbstractReport
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
    protected $title = 'Materials Shipping Grid Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Shipping information';


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
        'brands',
        'speakers',
        'fields',
        'programLocations',
        'programSpeakers',
        'programType',
        'timezone',
        'registrations',
        'presentations',
        'cancellations',
        'shipments',

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
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Materials Shipping Grid', function ($sheet) {
                        $sheet->loadView('reports.program.materials-shipping-grid.report')
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
					->has('brands')
					->anyReport( $arguments )
                    ->with($this->relations)
					->notInStatus([1, 6])
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
            'E' => static::AS_DATE,
            'F' => static::AS_TIME,
        ];
    }
}
