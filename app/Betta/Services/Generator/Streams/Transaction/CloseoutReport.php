<?php

namespace Betta\Services\Generator\Streams\Conference;

use Maatwebsite\Excel\Excel;
use Betta\Models\Conference;
use Betta\Services\Generator\Foundation\AbstractReport;

class CloseoutReport extends AbstractReport
{
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Closeout Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Closeout Report';


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
        'reps',
        'conferencecloseout',
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
                    $excel->sheet('Closeout Grid', function ($sheet) {
                        $sheet->loadView('reports.conference.closeout.report')
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
        return $this->conference
					->byBrand(array_get($arguments, 'inBrand' ) )
                    ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
					->with($this->relations)
					->Closeout()
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
            'B' => static::AS_DATE,
			'C' => static::AS_DATE,
        ];
    }
}
