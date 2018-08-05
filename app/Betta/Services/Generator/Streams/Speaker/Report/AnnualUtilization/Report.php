<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\AnnualUtilization;

use Carbon\Carbon;
use Betta\Models\Brand;
use Betta\Models\Profile;
use Betta\Models\Contract;
use Maatwebsite\Excel\Excel;
use Betta\Models\ProgramSpeaker;
use Betta\Models\NominationStatus;
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
     * @var Betta\Models|Brand
     */
    protected $brand;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Annual Utilization';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display Annual Speaker Utilization.';

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
        'nominations.brand.trainingCourses',
        'nominations.contracts.maxCaps',
        'nominations.profile.hcpProfile',
        'nominations.profile.attestations',
        'nominations.profile.trainings.trainingCourse.brands',
        'nominations.profile.speaks.costs',
        'nominations.profile.speaks.program',
        'nominations.profile.speakerBureaus',
        'nominations.profile.speaks.program.presentations',
        'nominations.profile.speaks.program.brands.programTypes',
        'nominations.profile.speaks.program.brands.programTypes',
        'nominations.owner.territories.parent.primaryProfiles.territories.parent.primaryProfiles',
    ];

    /**
     * Set the Tab formats
     *
     * @var array
     */
    protected $formats = [
        'D' => self::AS_DATE,
        'F' => self::AS_NICE_INTEGER,
        'G' => self::AS_CURRENCY,
        'H' => self::AS_NICE_INTEGER,
        'I:J' => self::AS_CURRENCY,
        'K' => self::AS_NICE_INTEGER,
        'L' => self::AS_CURRENCY,
        'M' => self::AS_DATE,
        'W' => self::AS_CURRENCY,
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Brand $brand)
    {
        $this->excel = $excel;
        $this->brand = $brand;
    }

    /**
     * Produce the Report
     *
     * @return Excel
     */
    protected function process()
    {
        $report = $this->excel->create( $this->getReportName(), function($excel){
            # Share properties
            $this->setProperties($excel);
            # The logic and flow:
            # 1. Grab the brands from, the arguments
            $this->getBrands()->each(function($brand) use($excel){
            # 2. For each brand in Arugments, produce two tabs:
            #   2.1. Active Annual Tab
                $excel->sheet($this->sanitizeLabel($brand, 'Active'), function($sheet) use($brand){
            #       Include all Speakers in Active Nominations with their Speaker Programs,
            #       regardless of the contract relation, scoped to the period
            #       For each of those Grouped items, display the utilization
            #       - Multiple record per person are NOT possible
                    $sheet->setColumnFormat($this->getFormats())
                          ->fromArray($this->activeNominations($brand)->toArray())
                          ->freezeFirstRow()
                            # Make sure to use it last
                            # This is because Excel cannot always figure out that we have data. So.
                          ->setAutoFilter();
                });
            #   2.2. Pending Speakers Annual Tab
                $excel->sheet($this->sanitizeLabel($brand, 'Pending'), function($sheet) use($brand){
            #       Include all Speakers in Active Nominations with their Speaker Programs,
            #       regardless of the contract relation, scoped to the period
            #       For each of those Grouped items, display the utilization
            #       - Multiple record per person are NOT possible
                    $sheet->setColumnFormat($this->getPendingFormats())
                          ->fromArray($this->pendingNominations($brand)->toArray())
                          ->freezeFirstRow()
                            # Make sure to use it last
                            # This is because Excel cannot always figure out that we have data. So.
                          ->setAutoFilter();
                });
            });
            # Also include SQL print out
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);

        })->store('xlsx', $this->getReportPath(), true );

        return array_set($report, 'data', $this->getCandidates());
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        return [];
    }

    /**
     * Load the Brands into the Report
     *
     * @return Collection
     */
    public function getBrands()
    {
        return $this->brand
                    ->byKey(data_get($this->arguments,'inBrand', []))
                    ->with($this->relations)
                    ->with(['nominations'=>function($nomination){
                        # Valid
                        $nomination->valid()->with(['profile.speaks'=>function($programSpeaker){
                            # where Profile Speaks are confirmed, cancelled in a progam...
                            $programSpeaker->confirmedOrCancelled()->whereHas('program', function($program){
                                # ... program that is current to year and in Brand we report upon
                                $program->inCalendarYear( data_get($this->arguments, 'inYear', date('Y')))
                                        ->inBrand(data_get($this->arguments,'inBrand', []));
                            });
                        }]);
                    }])->get();
    }

    /**
     * Sanitize the Label for the Sheets
     *
     * @param  Brand $brand
     * @return string
     */
    protected function sanitizeLabel(Brand $brand, $prefix='', $suffix='')
    {
        # sanitize String
        $string = $brand->acronym ?: nc_slug($brand->label, ' ');
        # append non-empty suffix
        return trim("{$prefix} {$string} {$suffix}");
    }

    /**
     * Transform Active Nominations Data
     *
     * @param  Brand  $brand
     * @return Collection
     */
    protected function activeNominations(Brand $brand)
    {
        $collection = $brand->nominations->where('is_active', true)->groupBy('profile_id')->map(function($collection){
            return (new Handlers\ActiveNominationsRow($collection))->fill();
        })->sortBy('Last Name');
        # set Candidates
        $this->setCandidates($collection->union($this->getCandidates()));
        # return
        return $collection;
    }

    /**
     * Transform Pending Nominations Data
     *
     * @param  Brand  $brand
     * @return Collection
     */
    protected function pendingNominations(Brand $brand)
    {
       #  Transform
        $collection = $brand->nominations->where('is_pending_onboarding', true)
                            ->groupBy('profile_id')->map(function($collection){
            return (new Handlers\PendingNominationsRow($collection))->fill();
        })->sortBy('Last Name');
        # set Candidates
        $this->setCandidates($collection->union($this->getCandidates()));
        # return
        return $collection;
    }

    /**
     * Formats for the Additional Tab
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return Array
     */
    protected function getPendingFormats()
    {
       return [
            'F' => self::AS_NICE_INTEGER,
            'G' => self::AS_CURRENCY,
            'H' => self::AS_NICE_INTEGER,
            'I:J' => self::AS_CURRENCY,
            'K' => self::AS_NICE_INTEGER,
            'L' => self::AS_CURRENCY,
            'M' => self::AS_DATE,
            'W' => self::AS_CURRENCY,
        ];
    }
}
