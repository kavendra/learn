<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Utilization;

use Carbon\Carbon;
use Betta\Models\Brand;
use Betta\Models\Profile;
use Betta\Models\Contract;
use Maatwebsite\Excel\Excel;
use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Foundation\AbstractReport;

class UtilizationReport extends AbstractReport
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
    protected $title = 'Speaker Utilization Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display Speaker Utilization.';

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
        'contracts.maxcaps',
        'contracts.profile.hcpProfile',
        'contracts.programSpeakers.costs',
        'contracts.programSpeakers.profile.hcpProfile',
        'contracts.programSpeakers.program.brands.programTypes',
        'contracts.programSpeakers.program.brands.programTypes',
        'contracts.baseNominations.owner',
        'nominations.owner.territories.parent.primaryProfiles.territories.parent.primaryProfiles',
        'nominations.profile.hcpProfile',
        'nominations.profile.speaks.costs',
        'nominations.profile.speaks.program',
        'nominations.profile.speaks.program.presentations',
        'nominations.profile.speaks.program.brands.programTypes',
        'nominations.profile.speaks.program.brands.programTypes',
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
        $this->brand = $brand;
        $this->excel   = $excel;
    }

    /**
     * Produce the Report
     *
     * @return Excel
     */
    protected function process()
    {

		# make new template
		return $this->excel->create( $this->getReportName(), function($excel){
                # Share properties
                $this->setProperties($excel);
                # The logic and flow:
                # 1. Grab the brands from, the arguments
                $this->getBrands()->each(function($brand) use($excel){
                # 2. For each brand in Arugments, produce two tabs:
                #   2.1 Contract tab
                    $excel->sheet($this->sanitizeLabel($brand, 'Contracts'), function($sheet) use($brand){
                #       Incude all the contracts that intersect with the requested
                #       For each of those contracts, display the utilization
                #       - Multiple records per person are possible
                        $sheet->setColumnFormat( $this->getFormats() )
                              ->fromArray($this->transformContracts($brand)->toArray() )
                              ->freezeFirstRow()
                                # Make sure to use it last
                                # This is because Excel cannot always figure out that we have data. So.
                              ->setAutoFilter();
                    });

                #   2.2. Annual Tab
                    $excel->sheet($this->sanitizeLabel($brand, ' Annual Utilization'), function($sheet) use($brand){
                #       Include all Speakers in Active Nominations with their Speaker Programs,
                #       regardless of the contract relation, scoped to the period
                #       For each of those Grouped items, display the utilization
                #       - Multiple record per person are NOT possible
                        $sheet->setColumnFormat( $this->getAnnualFormats() )
                              ->fromArray($this->transformNominations($brand)->toArray())
                              ->freezeFirstRow()
                                # Make sure to use it last
                                # This is because Excel cannot always figure out that we have data. So.
                              ->setAutoFilter();
                    });
                });
                # Insert Definitions
                $excel->sheet('Definitions', function($sheet){
                    $sheet->loadView('reports.speaker.utilization.definitionsTab' );
                });

                # Also include SQL print out
                $this->includeSqlTab($excel);

				# Make the first sheet active
				$excel->setActiveSheetIndex(0);

			})->store( 'xlsx', $this->getReportPath(), true );
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
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return [
          'G:H' => self::AS_DATE,
          'I' => self::AS_NICE_INTEGER,
          'J' => self::AS_CURRENCY,
          'K' => self::AS_NICE_INTEGER,
          'L:M' => self::AS_CURRENCY,
          'N' => self::AS_NICE_INTEGER,
          'O:Q' => self::AS_CURRENCY,
        ];
    }

    /**
     * Formats for the Additional Tab
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return Array
     */
    protected function getAnnualFormats()
    {
        return [
            'E' => self::AS_NICE_INTEGER,
            'F' => self::AS_CURRENCY,
            'G' => self::AS_NICE_INTEGER,
            'H:I' => self::AS_CURRENCY,
            'J' => self::AS_NICE_INTEGER,
            'K' => self::AS_CURRENCY,
            'L' => self::AS_DATE,

        ];
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
                    ->with(['contracts'=>function($contract){
                        $contract->anyReport($this->arguments)->noCancelledOrDeclined();
                    }])
                    ->with(['nominations'=>function($nomination){
                        $nomination->active()
                                    ->valid()
                                    ->with(['profile.speaks'=>function($programSpeaker){
                                        # where Profile Speaks are confirmed, cancelled in a progam...
                                        $programSpeaker->confirmedOrCancelled()->whereHas('program', function($program){
                                            # ... program that is current to year and in Brand we report upon
                                            $program->inCalendarYear( data_get($this->arguments, 'inYear', date('Y')))
                                                    ->inBrand(data_get($this->arguments,'inBrand', []));
                                        });
                                    }]);
                    }])
                    ->get();
    }

    /**
     * Sanitize the Label for the Sheets
     *
     * @param  Brand $brand
     * @return string
     */
    protected function sanitizeLabel(Brand $brand, $suffix = '')
    {
        # sanitize String
        $string = $brand->acronym ?: nc_slug($brand->label, ' ');

        # append non-empty suffix
        return $this->sanitizeSheetName(trim("{$string} {$suffix}"));
    }

    /**
     * Transform Contract Data
     *
     * @param  Brand  $brand
     * @return Collection
     */
    protected function transformContracts(Brand $brand)
    {
        return $brand->contracts->transform(function($contract){
            return (new Handlers\ContractRow($contract))->fill();
        })->sortBy('Last Name');
    }

    /**
     * Transform Nominations Data
     *
     * @param  Brand  $brand
     * @return Collection
     */
    protected function transformNominations(Brand $brand)
    {
        return $brand->nominations->groupBy('profile_id')->transform(function($collection){
            return (new Handlers\AnnualRow($collection))->fill();
        })->sortBy('Last Name');
    }
}
