<?php

namespace App\Http\Controllers\Reports;

use Betta\Models\ReportHistory;
use App\Http\Controllers\Controller;

class HomeController extends Controller
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
    public function __construct()
    {
        // Pending Model
        // $this->reportHistory = $reportHistory;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('reports.index');
    }
}
