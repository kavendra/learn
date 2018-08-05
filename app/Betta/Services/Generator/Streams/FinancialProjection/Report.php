<?php

namespace Betta\Services\Generator\Streams\FinancialProjection;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Program;
use Betta\Models\NprCost;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{


  /**
   * Bind implementation
   *
   * @var Program
   */
    protected $program;

  /**
   * Bind implementation
   *
   * @var NprCost
   */
    protected $nprcost;

  /**
   * Bind implementation
   *
   * @var Collection
   */
    protected $nprcosts;

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
    protected $title = 'Financial Projection for the programs';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Show financial projection for the marketing of a brand.';

    /**
     * Always fetch these relations for the Main Resource
     *
     * @var array
     */
    protected $relations = array(
        'brands.programTypes',
        'programType',
        'programStatus',
        'costs',
    );

    /**
     * Revenue cycle in days
     *
     * @var int
     */
    protected $revenueCycle = 15;

    /**
     * Expenses cycle in days
     *
     * @var int
     */
    protected $expenseCycle = 21;


     /**
     * NprCost cycle in days
     *
     * @var int
     */
    protected $nprcostCycle = 15;

    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Program $program, NprCost $nprcost)
    {
        $this->excel   = $excel;
        $this->program = $program;
        $this->nprcost = $nprcost;
    }

    protected function process()
    {
        # make new template
        return $this->excel
            ->create( $this->getReportName(), function($excel){
                # Set standard properties on the file
                $this->setProperties($excel);

                #method could be repeated
                $excel->sheet('Summary', function($sheet){
                    $sheet->freezeFirstRow()
                        ->setColumnFormat($this->getSummaryFormats() )
                        ->rows($this->getSummary())
                        ->setAutoSize(true)
                        # Make sure to use it last
                        # This is becuase Excel cannot always figure out we have data. So.
                        ->setAutoFilter();
                    $this->setSummaryStyles($sheet);
                });

                $excel->sheet('Revenue-Details', function($sheet){
                    $sheet->freezeFirstRow()
                        ->setColumnFormat( $this->getRevenueFormats() )
                        ->rows( $this->getRevenueDetails() )
                        ->row(1, function($row) {
                            $row->setFontWeight('bold');
                        })
                        ->setAutoSize(true)
                        # Make sure to use it last
                        # This is becuase Excel cannot always figure out we have data. So.
                        ->setAutoFilter();
                });

                $excel->sheet('NPR-Cost-Details', function($sheet){
                    $sheet->freezeFirstRow()
                        ->setColumnFormat( $this->getNprcostFormats() )
                        ->rows( $this->getNprDetails() )
                        ->row(1, function($row) {
                            $row->setFontWeight('bold');
                        })
                        ->setAutoSize(true)
                        # Make sure to use it last
                        # This is becuase Excel cannot always figure out we have data. So.
                        ->setAutoFilter();
                });

                $excel->sheet('Expenses-Details', function($sheet){
                    $sheet->freezeFirstRow()
                        ->setColumnFormat( $this->getExpensesFormats() )
                        ->rows( $this->getExpensesDetails() )
                        ->row(1, function($row) {
                            $row->setFontWeight('bold');
                        })
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

        $from    = array_get($arguments, 'from');
        $to      = array_get($arguments, 'to');

        return $this->program
            ->with($this->relations)
            ->byBrand( $inBrand )
            ->betweenDates($from, $to)
            ->orderBy('start_date')
            ->with( $this->relations )
            ->get()
            ->map(function($program){
                $program->revenue_date  = $program->start_date->copy()->addDays($this->revenueCycle);
                $program->expenses_date = $program->start_date->copy()->addDays($this->expenseCycle);
                return $program;
            });
    }

    /**
        * Load NprCost data from database
        *
        * @param  array $arguments
        * @return Collection
    */
    protected function getNprCosts()
    {
        # handle repeated loading of the collection
        if( $this->nprcosts ) {
            return $this->nprcosts;
        }

        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');

        $from    = array_get($this->arguments, 'from');
        $to      = array_get($this->arguments, 'to');

        $relations = array();

        $this->nprcosts = $this->nprcost
                      ->with($relations)
                      ->byBrand( $inBrand )
                      ->betweenDates($from, $to)
                      ->orderBy('invoice_date')
                      ->with( $relations )
                      ->get()
                      ->map(function($cost){
                          $cost->nprcost_date = $cost->invoice_date->copy()->addDays($this->nprcostCycle);
                          return $cost;
                      });

      return $this->nprcosts;
    }

    /**
      * Get Revenue Details
      *
      * @return Collection
      */
    protected function getRevenueDetails()
    {
        return $this->candidates->map(function($program){
                return (new Handlers\RevenueRow($program))->fill();
            })
            ->prepend((new Handlers\RevenueRow( app(Program::class) ))-> getKeys())
            ->toArray();
    }

    /**
      * Get Expenses Details
      *
      * @return Collection
      */
    protected function getExpensesDetails()
    {
        return $this->candidates->map(function($program){
                return (new Handlers\ExpensesRow($program))->fill();
            })->prepend((new Handlers\ExpensesRow( app(Program::class) ))-> getKeys())
            ->toArray();
    }


    /**
      * Get NPR Details
      *
      * @return Collection
      */
    protected function getNprDetails()
    {


        return $this->getNprCosts()->map(function($nprcost){
                return (new Handlers\NprcostRow($nprcost))->fill();
            })->prepend((new Handlers\NprcostRow( app(NprCost::class) ))-> getKeys())
            ->toArray();
    }

    /**
      * Get Summary
      *
      * @return Array
      */
    protected function getSummary()
    {
        # get summary month wise for each month
        $rows =  $this->getIntervalsByMonth()->map(function($month){
            return (new Handlers\SummaryRow($month, $this->candidates, $this->getNprCosts()) )->fill();
        });

        #get sum of each valid column
        $totalRow      = array(
            'Total',
            $rows->sum('No of Program'),
            $rows->sum('Revenue'),
            $rows->sum('NPR Cost'),
            $rows->sum('Expenses'),
            $rows->sum('Cash Flow'),
        );

        #keys
        $keys   = (new Handlers\SummaryRow)->getKeys();

        $rows   = array_prepend($rows->toArray(), $keys);

        $rows[] = $totalRow;

        return $rows;
    }

    /**
      * Get Interval by month i.e. all moths under consideration for summary report
      *
      * @return Collection
      */
    protected function getIntervalsByMonth()
    {
        $intervals  = collect();

        $start_month = Carbon::parse(array_get($this->arguments, 'from'))
                                    ->startOfMonth();
        # Show till the last month of date range
        # If projection extends further we are not showing them now
        # Also the start month does not include projection from previous months
        $end_month   = Carbon::parse(array_get($this->arguments, 'to'))
                            ->startOfMonth();

        $month = $start_month->copy();

        while( $month->lte($end_month) ){
            $intervals->push($month->copy());
            $month->addMonth(1);
        }
        return $intervals;
    }

    /**
      * Set Summary Styles
      *
      * @param $sheet
      * @return Collection
      */
    protected function setSummaryStyles($sheet)
    {
        $sheet->row(1, function($row) {
            $row->setFontWeight('bold');
            $row->setBackground('#D3D3D3');
        });

        $sheet->row($sheet->getHighestRow(), function($row) {
            $row->setFontWeight('bold');
            $row->setBackground('#D3D3D3');
        });

        return $sheet;
    }

    /**
      * Get Summary Formats
      *
      * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
      * @return Collection
      */
    protected function getSummaryFormats()
    {
        return [
            'C:E' => self::AS_CURRENCY,
            'F'   => '"$"#,##0.00_-',   # to allow negative currency
        ];
    }

    /**
      * Get Revenue Formats
      *
      * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
      * @return Collection
      */
    protected function getRevenueFormats()
    {
        return [
            'C'   => self::AS_DATE,
            'E:J' => self::AS_CURRENCY,
        ];
    }

    /**
      * Get Expenses Formats
      *
      * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
      * @return Collection
      */
    protected function getExpensesFormats()
    {
        return [
            'C'   => self::AS_DATE,
            'E:P' => self::AS_CURRENCY,
        ];
    }


    /**
      * Get NprCost Formats
      *
      * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
      * @return Collection
      */
    protected function getNprcostFormats()
    {
        return [
            'B'   => self::AS_DATE,
            'D'   => self::AS_CURRENCY,
        ];
    }

}
