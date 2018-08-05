<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Optimization;

use Betta\Models\Brand;
use Betta\Models\Nomination;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Brand
     */
    protected $brand;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Nomination
     */
    protected $nomination;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Optimization';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display Speaker Optimization Report.';

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
        'contracts',
        'profile.addresses',
        'profile.primaryAddress',
        'profile.speakerBureaus.brand',
        'profile.speaks.program.brands.programTypes',
        'owner.territories.parent.primaryProfiles',
        'owner.territories.parent.parent.primaryProfiles',
    ];

    /**
     * Set the Tab formats
     *
     * @var array
     */
    protected $formats = [
        'I:J' => self::AS_DATE,
        'K:M' => self::AS_NICE_INTEGER,
    ];

    /**
     * Model Injection
     *
     * @param  Brand $brand
     * @param  Nomination $nomination
     * @return Void
     */
    public function __construct(Brand $brand, Nomination $nomination)
    {
        $this->brand = $brand;
        $this->nomination = $nomination;
    }

    /**
     * Produce the Report
     *
     * @return array
     */
    protected function process()
    {
        $report = app('excel')->create( $this->getReportName(), function($excel){
            # Share properties
            $this->setProperties($excel);
            # The logic and flow:
            # Get brands and for each
            $this->getBrands()->each(function($brand) use($excel){
                # For each brand in Arugments, produce:
                $excel->sheet($this->sanitizeLabel($brand), function($sheet) use($brand){
                    #   Include all Speakers in Active Nominations with their Speaker Programs,
                    #   regardless of the contract relation, scoped to the nomination period
                    #   For each of those Grouped items, display the utilization
                    #   - Multiple record per person are NOT permissible
                    $sheet->setColumnFormat($this->getFormats())
                          ->fromArray($this->activeNominations()->where('nomination.brand_id', $brand->getKey())->toArray())
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
        # return
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
     * Get Brands only
     *
     * @return Collection
     */
    protected function getBrands()
    {
        return $this->brand->byKey($this->argument('inBrand', []))->get();
    }

    /**
     * Load the Brands into the Report
     *
     * @return Collection
     */
    public function getNominations()
    {
        return $this->nomination
                    ->active()
                    ->valid()
                    ->inBrand($this->argument('inBrand', []))
                    ->with($this->relations)
                    ->with(['profile.speaks'=>function($programSpeaker){
                        # where Profile Speaks are confirmed, cancelled in a progam...
                        $programSpeaker->confirmedOrCancelled()->whereHas('program', function($program){
                            # ... speaker program that will be filtered to intersect with the nomination period
                            $program->speakerPrograms()
                                    ->inBrand($this->argument('inBrand', []));
                        });
                    }])
                    ->get();
    }

    /**
     * Make the argument
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed | null
     */
    protected function argument($key, $default = null)
    {
        return data_get($this->arguments, $key, $default);
    }

    /**
     * Transform Active Nominations Data
     *
     * @param  Brand  $brand
     * @return Collection
     */
    protected function activeNominations()
    {
        $collection = $this->getNominations()->map(function($nomination){
            return (new Handlers\RowHandler($nomination))->fill();
        })->sortBy('profile.last_name');
        # set Candidates
        $this->setCandidates($collection);
        # return
        return $collection;
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
}
