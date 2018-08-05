<?php
namespace Betta\Services\Generator\Streams\Grids\Core;

use Betta\Models\Reconciliation;
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
     * @var Model
     */
    protected $reconciliation;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'CORE Grid Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'All CORE in one report';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'B' => self::AS_DATE,
        'F:W'=> self::AS_DATE,
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'ccs',
        'rcs',
        'rms',
        'program',
        'program.brands',
        'histories',
        'program.brands',
        'histories.createdBy',
        'reconciliationStatus',
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Reconciliation $reconciliation)
    {
        $this->excel   = $excel;
        $this->reconciliation = $reconciliation;
    }

    /**
     * Produce the Report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            $excel->sheet('CORE Grid', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->fromArray($this->candidates)
                      ->setAutoFilter()
                      ->freezeFirstRow();
                $this->setCellStyles($sheet);
                #->loadView('reports.grids.cost.report')
                #->with('programs',  $this->candidates )
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
        return $this->reconciliation
                    ->whereHas('program', function( $program ){
                        $program->anyReport( $this->arguments );
                     })
                    ->with($this->relations)
                    ->get()
                    ->transform(function($reconciliation){
                            return (new Handlers\CoreGridReportHandler($reconciliation))->fill();
                    })
                    ->toArray();
    }

     /**
      * Set Cell Styles
      *
      * @param $sheet
      * @return Collection
      */
    protected function setCellStyles($sheet)
    {
        $getAllRecords = $this->loadMergeData( $this->arguments );
        $total         = count($getAllRecords)+1;
        $sheet->cells("J2:K$total", function($cells) {
                    $cells->setBackground('#FFC7CE');
                    $cells->setFontColor('#F00000');
                });
        $sheet->cells("P2:Q$total", function($cells) {
                    $cells->setBackground('#FFC7CE');
                    $cells->setFontColor('#F00000');
                });
        return $sheet;
    }
}
