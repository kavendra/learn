<?php

namespace App\Http\Controllers\Reports;

use App\Models\ReportHistory;
use Betta\Services\Generator\Generator;
use App\Http\Controllers\Controller;

abstract class ReportController extends Controller
{
    /**
     * Password shared across all reports
     *
     * @var String
     */
    static $password = '41560005';

    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $reportHistory;

    /**
     * Bind the implementation
     *
     * @var App\Services\Generator\Generator
     */
    protected $generator;

    /**
     * Type of the Report
     *
     * @var string
     */
    protected $type = '';

    /**
     * Option to encrypt if desired
     *
     * @var bool
     */
    protected $encrypt = false;

    /**
     * @param ReportHistory $reportHistory
     * @param Generator   $generator
     */
    public function __construct(ReportHistory $reportHistory, Generator $generator)
    {
        $this->reportHistory = $reportHistory;
        $this->generator     = $generator;
    }

    /**
     * Display Filter Page
     *
     * @return Response
     */
    abstract public function index();

    /**
     * Default Post will Return the Report
     *
     * @return FileDownload
     */
    public function store()
    {
        $data = $this->validateInputs();
        # Compile Input
        $report = array(
            'arguments' => $this->sanitizedInput(),
            'type'      => $this->type,
        );
        # Generate
        $report =  $this->generator->make($report);

        #Encrypt the file
        if($this->encrypt){
            $this->encryptFile( array_get($report, "full") );
        }

        # Respond
        return $this->recordAndDownload($report);
    }

     /**
     * validate Inputs for Report
     *
     * 
     */
    public function validateInputs()
    {
        return true;
    }

    /**
     * Default input sanitize can be overwritten
     *
     * @return Array
     */
    protected function sanitizedInput()
    {
        # Load input (if present)
        $input = request()->input();
        # return sanitized input
        return array_merge($this->getDefaults(), $input);
    }

    /**
     * Return default values
     *
     * @return array
     */
    protected function getDefaults()
    {
        return [];
    }

    /**
     * Return the Report History
     *
     * @return Collection
     */
    protected function getReportHistory()
    {

        return $this->reportHistory->ofType($this->type)
                    ->latestBy( object_get(auth()->user(), 'profile_id') )
                    ->get();
    }

    /**
     * Interface to record history and Download result
     *
     * @param  array $report
     * @return Response
     */
    protected function recordAndDownload($report)
    {
        return $this->record($report)->download( array_get($report, 'full') );
    }

    /**
     * Create the Record in Report History
     *
     * @param  $report
     * @return instance
     */
    protected function record($report)
    {
        $history = array(
            'function_class'    => get_class( $this->generator ),
            'function_name'     => $this->type,
            'fuction_arguments' => json_encode( $this->sanitizedInput() ),
            'report_name'       => array_get($report, 'file'),
            'report_uri'        => array_get($report, 'full'),
        );

        $this->reportHistory->create($history);

        # return instance
        return $this;
    }

    /**
     * Send to browser
     *
     * @param  stirng $uri
     * @return Response
     */
    protected function download($uri)
    {
        return response()->download($uri);
    }

    /**
     * Encrypt file at given location
     *
     * @return bool
     */
    protected function encryptFile($uri)
    {
        $base_command  = 'java  -jar '.storage_path('app/EncryptExcel').'/EncryptFile.jar';

        $command = sprintf("$base_command \"%s\" \"%s\" 2>&1", $uri, static::$password);

        shell_exec($command);
    }

}
