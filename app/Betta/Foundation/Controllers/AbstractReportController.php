<?php

namespace Betta\Foundation\Controllers;

use Betta\Models\ReportHistory;
use App\Http\Controllers\Controller;

class AbtractReportController extends Controller
{

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ReportHistory
     */
    protected $reportHistory;

    /**
     * Type of
     *
     * @var string
     */
    protected $type;

    /**
     * View index of the reports
     *
     * @var string
     */
    protected $view;

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
        $history = $this->reportHistory->ofType($this->type)->get();

        return view($this->view)->withHistory($history);
    }
}
