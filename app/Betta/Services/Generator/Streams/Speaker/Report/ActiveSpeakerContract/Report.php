<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\ActiveSpeakerContract;

use Betta\Models\Nomination;
use Betta\Models\NominationStatus;
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
    protected $nomination;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Active Speaker Contract List Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Active Speaker Contract information';

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
        'brand',
        'tier',
        'profile',
        'profile.addresses',
        'profile.repProfile',
        'profile.assistants',
        'profile.hcpProfile',
        'profile.userProfile',
        'profile.primaryAddress',
        'profile.speakerProfile',
        'profile.speakerBureaus',
    ];

     /**
     * statuses
     *
     * @var Array
     */
    protected $statuses = [
        NominationStatus::ACTIVE,
        NominationStatus::PENDING_CONTRACT,
        NominationStatus::PENDING_TRAINING,
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
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Nomination $nomination
     * @return Void
     */
    public function __construct(Excel $excel, Nomination $nomination)
    {
        $this->excel   = $excel;
        $this->nomination = $nomination;
    }

    /**
     * Prooduce report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            $excel->sheet('Active Speaker Contract', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->fromArray( $this->candidates )
                      ->setAutoFilter()
                      ->setWidth('A', 21)
                      ->setWidth('B', 33)
                      ->setWidth('C', 20)
                      ->setWidth('D', 20)
                      ->setWidth('E', 24)
                      ->setWidth('F', 30)
                      ->setWidth('G', 30)
                      ->setWidth('H', 20)
                      ->setWidth('I', 45)
                      ->setWidth('J', 16)
                      ->setWidth('K', 19)
                      ->setWidth('L', 15)
                      ->setWidth('M', 15)
                      ->setWidth('N', 19)
                      ->setWidth('O', 46)
                      ->setWidth('P', 38)
                      ->setWidth('Q', 66)
                      ->freezeFirstRow();
            });
            $excel->getActiveSheet()->getStyle('F2:F1000')->getAlignment()->setWrapText(true);
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
        return $this->nomination
                    ->valid( data_get($arguments, 'at') )
                    ->inBrand( data_get($arguments, 'inBrand') )
                    ->inStatus( $this->statuses )
                    ->with( $this->relations )
                    ->get()
                    ->load('speaks')
                    ->transform(function( $nomination ){
                        return (new Handlers\ActiveSpeakerContractHandler( $nomination ))->fill();
                    });
    }
}
