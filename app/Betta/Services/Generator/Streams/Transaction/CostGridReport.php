<?php

namespace Betta\Services\Generator\Streams\Conference;

use Maatwebsite\Excel\Excel;
use Betta\Models\Conference;
use Betta\Services\Generator\Foundation\AbstractReport;

class CostGridReport extends AbstractReport
{
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Cost Grid Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Cost Grid Report';


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
        'createdBy',
        'addresses',
        'brands',
        'conferenceStatus',
        'reps',
        'budgetjars',
        'pms',
        'costs',
    ];


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Conference $conference)
    {
        $this->excel = $excel;
        $this->conference = $conference;
    }


    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('HC Cost Grid', function ($sheet) {
                        $sheet->loadView('reports.conference.costgrid.report')
                              ->with('conferences',  $this->candidates )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    $this->includeSqlTab($excel);

                    # Make the first sheet active
                    $excel->setActiveSheetIndex(0);
					$excel->getActiveSheet()->getStyle('A2:AZ1000')->getAlignment()->setWrapText(true);

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
        return $this->conference
					->byBrand($inBrand)
                    ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
					->with($this->relations)
					->latest()
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
			"S" => static::AS_CURRENCY,
			"U" => static::AS_CURRENCY,
			"V" => static::AS_CURRENCY,
			"W" => static::AS_CURRENCY,
			"X" => static::AS_CURRENCY,
			"Y" => static::AS_CURRENCY,
			"Z" => static::AS_CURRENCY,
			"AA" => static::AS_CURRENCY,
			"AB" => static::AS_CURRENCY,
			"AC" => static::AS_CURRENCY,
			"AD" => static::AS_CURRENCY,
			"AE" => static::AS_CURRENCY,
			"AF" => static::AS_CURRENCY,
			"AG" => static::AS_CURRENCY,
			"AH" => static::AS_CURRENCY,
			"AI" => static::AS_CURRENCY,
			"AJ" => static::AS_CURRENCY,
        ];
    }
}
