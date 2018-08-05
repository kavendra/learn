<?php

namespace App\Http\Controllers\Reports\Transaction;

use Carbon\Carbon;
use Betta\Models\Conference;
use App\Http\Controllers\Reports\ReportController;

class TransactionListController extends ReportController
{

    /**
     * Type of the Report
     *
     * @var string
     */
    protected $type = 'transaction/list-report';


    /**
     * Display Filter Page
     *
     * @return Response
     */
    public function index()
    {
		$data = $this->sanitizedInput();

        # Set the Defaults
        /*$data = array(
            'filterFrom' => $this->getDefaultFrom(),
            'filterTo'   => $this->getDefaultTo(),
            'history'    => $this->getReportHistory(),
        );*/

        # Define view
        $view = 'reports.transaction.list.index';

        # render
        return view($view)->with($data)->with('history', $this->getReportHistory());
    }


	/**
     * Default input sanitize can be overwritten
     *
     * @return Array
     */
    protected function sanitizedInput()
    {
        $input = request()->input();

        if( $date = array_get($input, 'from') ){
            array_set($input , 'from', Carbon::parse($date));
        } else {
            array_set($input , 'filterFrom', $this->getDefaultFrom() );
        }

        if( $date = array_get($input, 'to') ){
            array_set($input , 'to', Carbon::parse($date)->endOfDay() );
        } else {
            array_set($input , 'filterTo', $this->getDefaultTo() );
        }

        # return sanitized input
        return $input;
    }


    /**
     * Default From Date
     *
     * @return string
     */
    protected function getDefaultFrom()
    {
        return Carbon::parse('January 1')->format('m/d/Y');
    }


    /**
     * Default To Date
     *
     * @return string
     */
    protected function getDefaultTo()
    {
        return Carbon::parse('December 31')->format('m/d/Y');
    }

	/**
     * Get the permitted Conference
     *
     * @return array
     */
    protected function getConference()
    {
        return Conference::get();
    }
}
