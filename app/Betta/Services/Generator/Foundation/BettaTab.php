<?php

 namespace Betta\Services\Generator\Foundation;

use Illuminate\Contracts\Support\Arrayable;
 use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
 use Betta\Services\Generator\Interfaces\ExcelFormatsInterface;

abstract class BettaTab implements ExcelFormatsInterface
{
    /**
     * List arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Classes\LaravelExcelWorksheet
     */
    protected $worksheet;

    /**
     * Title of the Worksheet
     *
     * @var string
     */
    protected $title = 'Worksheet';

    /**
     * Formats implementing
     *
     * @todo  Betta\Services\Generator\Contracts\ExcelFormats
     * @see   Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var   array
     */
    protected $formats = [];

    /**
     * Formats implementing
     *
     * @todo  Betta\Services\Generator\Contracts\ExcelStyles
     * @var   array
     */
    protected $styles = [];

    /**
     * Define  the builder for the tab
     *
     * @var string
     */
    protected $builder;

    /**
     * Tab data
     *
     * @var array
     */
    public $data = [];

    /**
     * Resolve data to array
     *
     * @var boolean
     */
    protected $asArray = true;

    /**
     * Create new Tab
     *
     * @param array   $arguments
     */
    public function __construct($arguments = [])
    {
        $this->arguments = $arguments;
    }

    /**
     * Get a single argument
     *
     * @param  string $key
     * @param  mixed | null $default
     * @return mixed
     */
    public function argument($key, $default = null)
    {
        return array_get($this->arguments, $key, $default);
    }

    /**
     * Build the tab
     *
     * @param  LaravelExcelWorksheet $excelTab
     * @param  boolean $asWorksheet
     * @return LaravelExcelWorksheet
     */
    public function build(LaravelExcelWorksheet $excelTab, $asWorksheet = false)
    {
        # resolve the data
        $this->data = $this->getData();
        # make the worksheet
        $this->worksheet = $this->make($excelTab);
        # return
        return $asWorksheet ? $this->worksheet() : $this;
    }

    /**
     * Build the tab
     *
     * @param  LaravelExcelWorksheet $excelTab
     * @return LaravelExcelWorksheet
     */
    protected function make(LaravelExcelWorksheet $excelTab)
    {
        return $excelTab->setTitle($this->title())
                        ->setStyle($this->styles())
                        ->setColumnFormat($this->formats())
                        ->fromArray($this->data())
                        ->setAutoFilter()
                        ->setAutoSize(true)
                        ->freezeFirstRow();
    }

    /**
     * Return worksheet is needed
     *
     * @return LaravelExcelWorksheet
     */
    public function worksheet()
    {
        return $this->worksheet;
    }

    /**
     * Tab formats array
     *
     * @return array
     */
    protected function formats()
    {
        return $this->formats;
    }

    /**
     * Tab title
     *
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * Tab style
     *
     * @return array
     */
    protected function styles()
    {
        return $this->styles;
    }

    /**
     * Resolve the data for the tab
     *
     * @return array
     */
    protected function data()
    {
        return ($this->asArray AND $this->data instanceOf Arrayable) ? $this->data->toArray() : $this->data;
    }

    /**
     * Define the method for getting the data
     *
     * @return mixed
     */
    abstract public function getData();

    /**
     * Make SQL builder
     *
     * @param  boolean $resolve
     * @return Builder | Illuminate\Support\Collection
     */
    protected function builder($arguments, $resolve = true)
    {
        # make the builder
        $builder = app($this->builder)->make($arguments);
        # return the data
        return $resolve ? $builder->get() : $builder;
    }
}
