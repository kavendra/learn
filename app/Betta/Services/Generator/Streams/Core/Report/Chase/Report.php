<?php
namespace Betta\Services\Generator\Streams\Core\Report\Chase;

use Auth;
use Betta\Models\Program;
use Betta\Models\Reconciliation;
use Betta\Models\ReconciliationStatus;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;
use App\Http\Controllers\Program\Scopes\AbstractScopesController;

class Report extends AbstractReport
{

    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Reconciliation
     */
    protected $reconciliation;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'CORE Chase Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Core Chase Report';

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
        'program',
        'program.brands',
        'reconciliationStatus',
    ];

    /**
     * Formats of the resulting tabs
     *
     * @var array
     */
    protected $formats = [
        'C' => self::AS_DATE,
    ];

    /**
     * Create new instance of Report
     *
     * @param  Excel   $excel
     * @param  Reconciliation $reconciliation
     * @return Void
     */
    public function __construct(Excel $excel, Reconciliation $reconciliation)
    {
        $this->excel   = $excel;
        $this->reconciliation = $reconciliation;
    }

    /**
     * Produce the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes
            $excel->sheet('Summary', function ($sheet) {
                $sheet->loadView('reports.core.chase.summary')
                      ->with('unclaimedbycc',    $this->loadUnclaimedByCCList()->count())
                      ->with('unclaimedbyrc1',   $this->loadUnclaimedByRC1List()->count())
                      ->with('unclaimedbyrc2',   $this->loadUnclaimedByRC2List()->count())
                      ->with('closeoutRejected', $this->loadCloseoutRejectedList()->count())
                      ->with('reconRejected',    $this->loadReconRejectedList()->count())
                      ->freezeFirstRow()
                      ->setAutoFilter();
                    });

            $excel->sheet('Unclaimed Closeout', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->loadUnclaimedByCCList()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Unclaimed Initial Recon', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->loadUnclaimedByRC1List()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Unclaimed Final Recon', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->loadUnclaimedByRC2List()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Closeout Rejected', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->loadCloseoutRejectedList()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Initial Recon Rejected', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats() )
                      ->fromArray( $this->loadReconRejectedList()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
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
         return [];
    }

    /**
     * Load Unclaimed Closeouts
     *
     * @return Collection
     */
    protected function loadUnclaimedByCCList()
    {
        return $this->reconciliation
                    ->whereHas('program', function($query){
                        $query->unreconciled();
                    })
                    ->with($this->relations)
                    ->unclaimedCCS()
                    ->get()
                    ->transform(function($reconciliation){
                        return (new Handlers\UnclaimedRow($reconciliation))->fill();
                    });
    }

    /**
     * Load Unclaimed Initial Recon
     *
     * @return Collection
     */
    protected function loadUnclaimedByRC1List()
    {
        return $this->reconciliation
                    ->whereHas('program', function($query){
                        $query->unreconciled();
                    })
                    ->with($this->relations)
                    ->unclaimedRCS()
                    ->get()
                    ->transform(function($reconciliation){
                        return (new Handlers\UnclaimedRow($reconciliation))->fill();
                    });
    }

    /**
     * Load Unclaimed Final Recon
     *
     * @return Collection
     */
    protected function loadUnclaimedByRC2List()
    {
        return $this->reconciliation
                    ->whereHas('program', function($query){
                        $query->unreconciled();
                    })
                    ->with($this->relations)
                    ->unclaimedRMS()
                    ->get()
                    ->transform(function($reconciliation){
                        return (new Handlers\UnclaimedRow($reconciliation))->fill();
                    });
    }

    /**
     * Load Closeout Rejected
     *
     * @return Collection
     */
    protected function loadCloseoutRejectedList()
    {
        return $this->reconciliation
                    ->reconcilable()
                    ->with($this->relations)
                    ->where('reconciliation_status_id', ReconciliationStatus::CLOSEOUT_REJECTED)
                    ->get()
                    ->transform(function($reconciliation){
                        return (new Handlers\UnclaimedRow($reconciliation))->fill();
                    });
    }

    /**
     * Load Initial Recon Rejected
     *
     * @return Collection
     */
    protected function loadReconRejectedList()
    {
        return $this->reconciliation
                    ->reconcilable()
                    ->with($this->relations)
                    ->where('reconciliation_status_id', ReconciliationStatus::RECONCILIATION_REJECTED)
                    ->get()
                    ->transform(function($reconciliation){
                        return (new Handlers\UnclaimedRow($reconciliation))->fill();
                    });
    }
}
