<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Maatwebsite\Excel\Excel;
use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Foundation\AbstractReport;

class BrandHonorarium extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Brand Honorarium Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information in Brand Honorarium Report';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $relations = array(
        'costs',
        'expenses',
        'documents',
        'progressions',
        'program.pms',
        'program.costs',
        'program.brands',
        'program.fields.repProfile',
        'profile.addresses',
        'program.registrations',
        'program.programLocations.address',
    );

    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, ProgramSpeaker $programSpeaker)
    {
        $this->excel = $excel;
        $this->programSpeaker = $programSpeaker;
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
            # Set standard properties on the file
            $this->setProperties($excel);
            #method could be repeated
            $excel->sheet('Brand Honorarium Report', function($sheet){
                $sheet->loadView('reports.speaker.brand-honorarium.report')
                      ->withProgramSpeakers( $this->candidates )
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() )
                      # Make sure to use it last
                      # This is becuase Excel cannot always figure out we have data. So.
                      ->setAutoFilter();
            });

            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
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
        return $this->programSpeaker
                    # Scope by Brand
                    ->byBrand( $this->getBrands() )
                    # Scope by Program Date
                    ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
                    # Ensure Confirmed Or Cancelled with costs
                    ->where(function($programSpeaker){
                        $programSpeaker->confirmed()->orCancelled();
                    })
                    # with Relations
                    ->with($this->relations)
                    ->get()
                    ->sortBy('program.start_date');
    }

    /**
     * Resolve Brands from the arguments
     *
     * @return array
     */
    protected function getBrands()
    {
        return array_get($this->arguments, 'inBrand', []);
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
          'B'   => static::AS_DATE,
          'H'   => static::AS_NICE_INTEGER,
          'P:R' => static::AS_CURRENCY,
          'S'   => static::AS_DATE,
          'W:X' => static::AS_DATE,
        ];
    }
}
