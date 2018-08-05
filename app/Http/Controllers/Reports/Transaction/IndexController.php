<?php

namespace App\Http\Controllers\Reports\Transaction;

use Betta\Models\ReportHistory;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ReportHistory
     */
    protected $reportHistory;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ReportHistory $reportHistory)
    {
        $this->reportHistory = $reportHistory;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $history = $this->reportHistory->ofType('conference/*')->get();
		
        return view('reports.conference.index')->withHistory($history);
    }
}
