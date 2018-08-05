<?php

 namespace Betta\Services\Generator\Foundation;

use DB;
use PHPExcel_Shared_String;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Betta\Services\Generator\Interfaces\ExcelFormatsInterface;

abstract class AbstractReport implements ExcelFormatsInterface
{
    /**
     * Share Errors
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title;

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Relations of the base model
     *
     * @var array
     */
    protected $realtions = [];

    /**
     * Share the arguments accross the class
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Relative Path to store the report
     *
     * @var string
     */
    protected $storagePath = 'export/reports';

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [];

    /**
     * Store the data for the report
     *
     * @var Arrayable
     */
    protected $candidates = [];

    /**
     * Produce the report
     *
     * @return
     */
    abstract protected function process();

    /**
     * Load the Actual Data
     *
     * @param  array $arguments
     * @return Collection
     */
    abstract protected function loadMergeData($arguments);

    /**
     * Handling should be simple and should return result of processing
     * But also, allow for overloading
     *
     * @param  array  $arguments
     * @return Array
     */
    public function handle($arguments = array())
    {
        # Set the Arguments
        $this->arguments = $arguments;
        # Set the query log to true
        if($this->includeSql()){
            DB::connection()->enableQueryLog();
        }
        # Share the candidates accross, so that we can cache the variable on the class level
        return $this->setCandidates($this->getMergeData($arguments))->process();
    }

    /**
     * Access a single argument
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function argument($key, $default = null)
    {
        return data_get($this->arguments, $key, $default);
    }

    /**
     * Set the Candidates
     *
     * @param mixed $candidates
     * @return $this
     */
    public function setCandidates($candidates)
    {
        $this->candidates = $candidates;

        return $this;
    }

    /**
     * Get the Candidates
     *
     * @return Illuminate\Support\Collection
     */
    public function getCandidates()
    {
        return collect($this->candidates);
    }

    /**
     * Return errors
     *
     * @return MessageBag
     */
    public function getErrors()
    {
        return $this->errors->getMessageBag();
    }

    /**
     * Return the final path for the Report
     *
     * @return string
     */
    protected function getReportPath()
    {
        return storage_path( $this->storagePath );
    }

    /**
     * Return the name of Report
     *
     * @return String
     */
    protected function getReportName()
    {
        return sprintf('%s as of %s @ %s', $this->title, date('F, j Y'), date('g-i A') );
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function getMergeData($arguments)
    {
        if ($mergeData = array_get($arguments, 'mergeData')) {
            # load the necessary relations
            $mergeData->load($this->relations);
            # return MergeData
            return array_get($arguments, 'transform') ? $this->transform($mergeData) : $mergeData;
        }

        return $this->loadMergeData($arguments);
    }

    /**
     * Excellent for method overloading
     *
     * NOTA BENE: using transform() method of a collection WILL affect upper scope and will CHANGE it
     *
     * @param  mixed $data
     * @return mixed
     */
    protected function transform($data)
    {
        return $data;
    }

    /**
     * If the SQL Printout is requested, it will be included
     *
     * @param  LaravelExcelWriter $excel
     * @return Void
     */
    protected function includeSqlTab(LaravelExcelWriter $excel)
    {
        if ($this->includeSql()) {
            $excel->sheet('SQL Printout', function ($sheet) {
                $sheet->loadView('partials.helpers.query-log');
            });
        }
    }

    /**
     * True if the report explicitly allows SQL OR environment allows debudding
     *
     * @return boolean
     */
    protected function includeSql()
    {
        return $this->includeSql ?: env('APP_DEBUG');
    }

    /**
     * Beautify the File properties
     *
     * @param  Excel $excel
     * @return Void
     */
    protected function setProperties(LaravelExcelWriter $excel)
    {
        # Nice properties of Report
        $excel->setTitle($this->title)
              ->setCreator( trans('app.manager') )
              ->setCompany( trans('app.owner') )
              ->setDescription( $this->description );
    }

    /**
     * Match columns to formats
     *
     * @return array
     */
    protected function getFormats()
    {
        return $this->formats;
    }

    /**
     * List columns with autosize
     *
     * @return array
     */
    protected function getAutosizes()
    {
        return [];
    }

    /**
     * Sanitize the Sheet Name
     *
     * @param string
     * @return string
     */
    protected function sanitizeSheetName($sheet_name)
    {
        // Set of Excel characters PHPExcel will not accept
        $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');
        // Some of the printable ASCII characters are invalid: defined in var _invalidCharacters
        if (str_replace($_invalidCharacters, ' ', $sheet_name) !== $sheet_name) {
            //Hack to remove bad characters from sheet name instead of throwing an exception
            return str_replace($_invalidCharacters, '', $sheet_name);
        }
        // Maximum 31 characters allowed for sheet title
        if (PHPExcel_Shared_String::CountCharacters($sheet_name) > 31) {
            return str_limit($sheet_name,30,'');
        }
        return $sheet_name;
    }
}
