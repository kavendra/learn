<?php

namespace Betta\Services\Generator\Streams\Usage\IsigninApp;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{


  /**
   * Bind implementation
   *
   * @var Model
   */
    protected $program;

  /**
   * Bind implementation
   *
   * @var Maatwebsite\Excel\Excel
   */
    protected $excel;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'iSignin App vs Portal Usage Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information about usage of iSignin App vs paper sign-in sheets.';

    /**
     * Column Formats
        *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'C' => self::AS_DATE,
        'H' => self::AS_CURRENCY,
    ];


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;


    /**
   * Always fetch these relations for the Main Resource
   *
   * @var array
   */
  protected $relations = array(
        'brands.programTypes',
        'fields',
        'costs',
        'registrations',
  );


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Program $program)
    {
        $this->excel   = $excel;
        $this->program = $program;
    }

    protected function process()
    {
        # make new template
        return $this->excel
            ->create( $this->getReportName(), function($excel){
                # Set standard properties on the file
                $this->setProperties($excel);

                #method could be repeated
                $excel->sheet('iSignIn vs Paper', function($sheet){
                    $sheet->freezeFirstRow()
                        ->setColumnFormat( $this->getFormats() )
                        ->fromArray($this->candidates->toArray())
                        ->setAutoSize(true)
                        # Make sure to use it last
                        # This is becuase Excel cannot always figure out we have data. So.
                        ->setAutoFilter();
                });

                # Set the includeSql = true to have SQL Printout tab
                # includeSql should NEVER be true for production reports
                $this->includeSqlTab($excel);

                # Make the first sheet active
                $excel->setActiveSheetIndex(0);
            })
            ->store( 'xlsx', $this->getReportPath(), true );

    }

    /**
        * Load the data from the database
        *
        * @param  array $arguments
        * @return Collection
    */
    protected function loadMergeData( $arguments )
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');
        $from    = Carbon::parse(array_get($arguments, 'from'))->startOfDay();
        $to      = Carbon::parse(array_get($arguments, 'to'))->endOfDay();

        return $this->program
            ->with($this->relations)
            ->byBrand( $inBrand )
            ->betweenDates($from, $to)
            ->orderBy('start_date')
            ->with( $this->relations )
            ->get()
            ->transform(function($program){
                return (new Handlers\Row($program))->fill();
            });
    }

}
