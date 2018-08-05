<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\Program;
use Betta\Models\Payment;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class PaymentsReport extends AbstractReport
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
    protected $payment;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Payments Grid Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display payment information for LSP speakers.';

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
        'profile',
    	'context',
    	'shipments',
    ];

    /**
     * Create new Report
     *
     * @param  Excel   $excel
     * @param  Payment $payment
     * @return Void
     */
    public function __construct(Excel $excel, Payment $payment)
    {
        $this->excel   = $excel;
		$this->payment = $payment;
    }

    /**
     * Produce the report
     *
     * @return Object
     */
    protected function process()
    {
		return $this->excel->create( $this->getReportName(), function($excel){
			# Set standard properties on the file
            $this->setProperties($excel);
			# method could be repeated
			$excel->sheet('Program Payments', function($sheet){
			    return $this->programPayments($sheet);
			})->sheet('Engagement Payments', function($sheet){
			    return $this->engagementPayments($sheet);
			});
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
		})
		->store( 'xlsx', $this->getReportPath(), true );
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        return collect([]);
        // return $this->payment->get()->filter(function($payment){
        //                 return $payment->context->start_date->between($this->argument('from'), $this->argument('to'));
        //             })->load($this->relations);
    }

	/**
	 * Produce the Program Payments Sheet
	 *
	 * @param  Maatwebsite\Excel\Classes\LaravelExcelWorksheet $page
	 * @return Maatwebsite\Excel\Classes\LaravelExcelWorksheet
	 */
	protected function programPayments($page)
	{
		$payments = $this->payment->whereContextType(Program::class)->get()->filter(function($payment){
                        return $payment->context->start_date->between($this->argument('from'), $this->argument('to'));
                    })->load($this->relations);
        # Render
		return $page->loadView('reports.speaker.payment.programs')
					->with('payments', $payments)
					->freezeFirstRow()
					->setColumnFormat([
                        'B' => self::AS_DATE,
						'J' => self::AS_CURRENCY,
						'L' => self::AS_DATE,
						'M' => self::AS_DATE,
					])
					->setAutoFilter();
    }

	/**
	 * Produce the Engagement Payments Sheet
	 *
	 * @param  Maatwebsite\Excel\Classes\LaravelExcelWorksheet $page
	 * @return Maatwebsite\Excel\Classes\LaravelExcelWorksheet
	*/
	protected function engagementPayments($page)
	{
        $payments = collect([]);
        # $payments = $this->payment->whereContextType(Engagement::class)->get();
        # Render
		return $page->loadView('reports.speaker.payment.engagements')
					->with('payments', $payments)
					->freezeFirstRow()
					->setColumnFormat([
                        'B' => self::AS_DATE,
						'G' => self::AS_CURRENCY,
						'I' => self::AS_DATE,
						'J' => self::AS_DATE,
					])
					# Make sure to use it last
					# This is becuase Excel cannot always figure out we have data. So.
					->setAutoFilter();
    }
}
