<?php

namespace Betta\Services\Generator\Streams\Speaker\Report;

use Carbon\Carbon;
use Betta\Models\Nomination;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class ActiveNominationReport extends AbstractReport
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
    protected $title = 'Active Nomination Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Active Nominations and project expirations.';

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
        'profile.speakerProfile',
        'brand',
        'owner',
        'optimizations',
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
        $this->excel      = $excel;
        $this->nomination = $nomination;
    }

    /**
     * Produce the report
     *
     * @return StdObject
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
                    # Set standard properties on the file
                    $this->setProperties($excel);

                    # 1. Active at {Date},
                    # 2. Expires --> Date (+30 days)
                    # 3. Expires --> Date (31 + 60 days), using months
                    # 4. Expires --> Date (61 + 90 days), using months
                    # 5. Expired before {Date}

                    # Produce the tab
                    $excel->sheet('Active Nominations', function ($sheet) {
                        $sheet->loadView('reports.speaker.active-nomination.report')
                              ->with('nominations', $this->candidates )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter();
                    });

                    $expiring = $this->loadExpiringNominations();

                    # Produce the tab, + 30
                    $excel->sheet('Expiring in 30 Days', function ($sheet) use ($expiring){
                        $sheet->loadView('reports.speaker.active-nomination.expiring')
                              ->with('nominations', $expiring->whereIn('days_until_expiry', range(1,30)) )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter();
                    });

                    # Produce the tab, + 31 -> 60
                    $excel->sheet('Expiring in 60 Days', function ($sheet) use ($expiring){
                        $sheet->loadView('reports.speaker.active-nomination.expiring')
                              ->with('nominations', $expiring->whereIn('days_until_expiry', range(31,60)) )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter();
                    });

                    # Produce the tab, +61 -> 90
                    $excel->sheet('Expiring in 90 Days', function ($sheet) use ($expiring){
                        $sheet->loadView('reports.speaker.active-nomination.expiring')
                              ->with('nominations', $expiring->whereIn('days_until_expiry', range(61,90)) )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter();
                    });

                    # Produce the Expired tab
                    $excel->sheet('Expired', function ($sheet) use ($expiring){
                        $sheet->loadView('reports.speaker.active-nomination.expired')
                              ->with('nominations', $this->loadExpiredNominations() )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() )
                              ->setAutoFilter();
                    });

                    # Produce Definitions tab
                    $excel->sheet('Definitions', function ($sheet) use ($expiring){
                        $sheet->loadView('reports.speaker.active-nomination.definitions');
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
                    ->active()
                    ->valid( data_get($arguments, 'at') )
                    ->inBrand( data_get($arguments, 'inBrand') )
                    ->with($this->relations)
                    ->get()
                    ->load('speaks');
    }

    /**
     * Return all nominations, expiring 90 days out
     *
     * @param  array $arguments
     * @return Collection
     */
    protected function loadExpiringNominations()
    {
        $at = Carbon::parse(data_get($this->arguments, 'at'))->addDays(90);

        return $this->nomination
                    ->active()
                    ->expiresBefore($at)
                    ->inBrand( data_get($this->arguments, 'inBrand') )
                    ->with($this->relations)
                    ->get()
                    ->load('speaks');
    }

    /**
     * Return All Expired Nomination (before date)
     *
     * @param  array $arguments
     * @return Collection
     */
    protected function loadExpiredNominations()
    {
        $at = Carbon::parse(data_get($this->arguments, 'at'))->subDay();

        return $this->nomination
                    ->expiresBefore($at)
                    ->inBrand( data_get($this->arguments, 'inBrand') )
                    ->with($this->relations)
                    ->get()
                    ->load('speaks');
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
            'E' => AbstractReport::AS_DATE,
            'F' => AbstractReport::AS_DATE,
            'G' => AbstractReport::AS_NICE_INTEGER,
        ];
    }
}
