<?php

namespace Betta\Services\Generator\Streams\Conference\NominationList;

use Betta\Models\Conference;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;
use Betta\Services\Generator\Foundation\AbstractRowHandler;
use Betta\Services\Generator\Streams\Conference\NominationList\Handlers\RowHandler;

class Report extends AbstractReport
{
    use ReportQueryBuilder;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Nomination List Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Conference Nomination information';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Variable
     *s
     * @var collection
     */
    private $_nominations;

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [

    ];

     /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [

    ];

    /**
     * Create new Instance of Excel

     * @return Void
     */
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
     * Produce the Report
     *
     * @return Array
     */
    /**
     * Produce the Report
     *
     * @return Array
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes

            # Sheet for each
            $excel->sheet("List ", function ($sheet){
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->getCandidates()->toArray())
                      ->freezeFirstRow()
                      ->setAutoFilter()
                      ->row(1, function($row){
                        $row->setFontWeight('bold');
                      });
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
        # get the results from builder
        $this->_nominations = $this->getBuilder($arguments)->with($this->relations)->get()
                                ->transform(function(Conference $conference){
                                    # future records
                                    $records = collect([]);
                                    # iterate
                                    foreach($conference->group_nominations as $profile){
                                        $records->push( with(new RowHandler($profile))->fill() );
                                    }
                                    # return a collection (could be one record)
                                    return $records;
                                })
                                ->collapse();

        if($this->_nominations->count() == 0) {
            $this->_nominations = [RowHandler::headers()];
        }
        return $this->_nominations;
    }
}
