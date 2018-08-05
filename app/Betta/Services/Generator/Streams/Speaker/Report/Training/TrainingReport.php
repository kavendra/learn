<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Training;

use Betta\Models\Profile;
use Betta\Models\Training;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class TrainingReport extends AbstractReport
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
    protected $profile;

    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $training;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Training Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker Training information';

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
        'trainings.profile.hcpProfile',
        'trainings.trainingCourse.brands',
        'trainings.profile.speakerProfile',
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Profile $profile
     * @return Void
     */
    public function __construct(Excel $excel, Profile $profile, Training $training)
    {
        $this->excel   = $excel;
        $this->profile = $profile;
        $this->training = $training;
    }

    /**
     * Create the report
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
                    $excel->sheet('Speaker Training Report', function ($sheet) {
                        $sheet->loadView('reports.speaker.training.report')
                              ->with('rows',  $this->candidates )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });

                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Attestations', function ($sheet) {
                        $sheet->loadView('reports.speaker.training.attestations')
                              ->with('rows',  $this->getProfileAttestations() )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getAttestationFormats() );
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
        return $this->profile->speakers()
                    ->with($this->relations)
                    ->get()
                    ->pluck('trainings')
                    ->collapse()
                    ->transform(function($training){
                        return (new Handlers\SpeakerTrainingRow($training))->fill();
                    })
                    ->sortBy('last_name');
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
            'G:H' => self::AS_DATE,
            'O' => self::AS_ZIP_CODE,
            'R:W' => self::AS_PHONE,
        ];
    }

    /**
     * Fetch Attestations
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    protected function getProfileAttestations()
    {
        return $this->profile
                    ->has('attestations')
                    ->with('attestations')
                    ->get()
                    ->transform(function($profile){
                        # We receive profile and transform the Many-to-Many relation
                        # Since we have the Profile, two models are injected into the row
                        return $profile->attestations->transform(function($attestation) use ($profile){
                            return (new Handlers\ProfileAttestationRow($attestation, $profile))->fill();
                        });
                    })
                    ->collapse()
                    ->sortBy('last_name');
    }

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getAttestationFormats()
    {
        return [
            'E' => self::AS_DATE,
        ];
    }
}
