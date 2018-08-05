<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\SpeakerList;

use Betta\Models\Nomination;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class SpeakerListsReport extends AbstractReport
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
    protected $title = 'Speaker List Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker information';

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
        'profile.user',
        'brand',
        'profile.addresses',
        'profile.hcpProfile',
        'profile.repProfile',
        'profile.speakerProfile',
        'profile.primaryAddress',
        'speakerClassifications',
        'speakerClassifications.speakerClassificationGroup',
        'profile.experiences',
        'owner.territories.parent.parent',
        'nominationStatus',
        'contracts.maxCaps',
        'profile.trainings',
        'profile.blockDays',
        'profile.w9s',
        'backgroundChecks',
    ];

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'O' => self::AS_ZIP_CODE,
        'Q:R' => self::AS_PHONE,
        'AC:AD' => self::AS_DATE,
        'AF:AI' => self::AS_DATE,
        'AK:AL' => self::AS_DATE,
        'AU' => self::AS_CURRENCY,
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
            # @todo exctract tabs into their own classes
            $excel->sheet($this->title, function ($sheet) {
                $sheet->loadView('reports.speaker.speaker-lists.report')
                      ->with('rows',  $this->candidates )
                      ->setColumnFormat( $this->getFormats() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
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
        return $this->nomination
                    ->with($this->relations)
                    ->valid( data_get($arguments, 'at') )
                    ->inBrand(array_get($arguments, 'inBrand'))
                    ->whereHas('profile.speakerProfile')
                    ->get()
                    ->transform(function($nomination){
                        return (new Handlers\NominationRow($nomination))->fill();
                    });
    }
}
