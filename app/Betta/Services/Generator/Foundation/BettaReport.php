<?php

 namespace Betta\Services\Generator\Foundation;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

abstract class BettaReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * List tab handlers
     *
     * @var array
     */
    protected $tabs = [];

    /**
     * List tab handlers
     *
     * @var array
     */
    protected $data = [];

    /**
     * Default format for the report
     *
     * @var string
     */
    protected $fileFormat = 'xlsx';

    /**
     * Return as Object with file paths
     *
     * @var string
     */
    protected $returnPaths = true;

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @return Void
     */
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
     * Make new Tab
     *
     * @param  string $name
     * @param  callable $handler
     * @return void
     */
    protected function tab($name, $handler)
    {
        # Make new sheet
        $sheet = $this->excel->createSheet();
        # create the handler, pass the arguments
        $worksheet = app($handler, ['arguments'=>$this->arguments])->build($sheet);
        # build
        return $this->fetch($worksheet, $name);
    }

    /**
     * Create the Structure with Data
     *
     * @return array
     */
    protected function process()
    {
        # Produce the report
        $report = $this->make()->store($this->fileFormat, $this->getReportPath(), $this->returnPaths);
        # if returning array:
        if(is_array($report)){
            array_set($report, 'data', $this->data);
        }
        # return report
        return $report;
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        # this method intentionally left void
    }

    /**
     * Make the Excel file
     *
     * @return LaravelExcelWriter
     */
    protected function make()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            foreach ($this->tabs as $name => $handler) {
                $this->tab($name, $handler);
            }
            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
        });
    }

    /**
     * Get the Tab data
     *
     * @param  BettaTab $tab
     * @return BettaTab
     */
    protected function fetch(BettaTab $tab, $name)
    {
        # fetch the data from
        $this->data[$name] = $tab->getData();
        # return tab
        return $tab;
    }
}
