<?php

namespace Betta\Services\Generator\Streams\Ticket\Master;

use Betta\Models\Brand;
use Betta\Models\Ticket;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class MasterTicketReport extends AbstractReport
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
     * @var Betta\Models\Ticket
     */
    protected $ticket;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Brand
     */
    protected $brand;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Master Ticket Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Ticket Report';

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
        'brands',
        'ticketCategory',
        'profiles',
        'ticketStatus'
    ];

    /**
     * Create new instance of Report
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Ticket $ticket, Brand $brand)
    {
        $this->excel  = $excel;
        $this->ticket = $ticket;
        $this->brand  = $brand;
    }

    /**
     * Process and make the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
                    # Set standard properties on the file
                    $this->setProperties($excel);
                    # Produce the tab
                    $excel->sheet('Ticket Report', function ($sheet) {
                        $sheet->loadView('reports.ticket.master.report')
                              ->with('rows',  $this->candidates )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
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
     * @return Collection
     */
    protected function loadMergeData($arguments)
    {
		$inBrand = array_get($arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');

        $inStatus = array_get($arguments, 'ticket_status' );
        $inStatus = is_array($inStatus) ? $inStatus : $inStatus->pluck('id');

        $inCategory = array_get($arguments, 'ticket_category' );
        $inCategory = is_array($inCategory) ? $inCategory : $inCategory->pluck('id');

        $count = $this->brand->count();

        return $this->ticket
                    ->byBrand($inBrand)
                    ->byStatus($inStatus)
                    ->byCategory($inCategory)
                    ->betweenDates( array_get($arguments, 'from'), array_get($arguments, 'to') )
                    ->with($this->relations)
                    ->get()
                    ->transform(function($ticket) use ($count){
                        return (new Handlers\TicketRow($ticket, $count))->fill();
                    });
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
            'H:I' => static::AS_DATE,
        ];
    }
}
