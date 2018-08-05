<?php
namespace Betta\Services\Generator\Streams\Grids\Core\Handlers;

use Betta\Models\Reconciliation;
use Betta\Models\ReconciliationStatus;
use Betta\Services\Generator\Foundation\AbstratRowHandler as Handler;

class CoreGridReportHandler extends Handler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Reconciliation
     */
    protected $reconciliation;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Program Date',
        'Program Brand',
        'Program Type',
        'Reconciled',
        'Closeout Initiated Date',
        'CORE Current Status',
        'Closeout Claimed By',
        'Closeout Claimed Date',
        'Closeout Rejected By',
        'Closeout Rejected Date',
        'Closeout Completed By',
        'Closeout Completed Date',
        'Initial Recon Claimed By',
        'Initial Recon Claimed Date',
        'Initial Recon Rejected By',
        'Initial Recon Rejected Date',
        'Initial Recon Completed By',
        'Initial Recon Completed Date',
        'Final Recon Claimed By',
        'Final Recon Claimed Date',
        'Final Recon Completed By',
        'Final Recon Completed Date',
    ];

    /**
     * Create new Row instance
     *
     * @param Reconciliation $reconciliation
     */
    public function __construct(Reconciliation $reconciliation)
    {
        $this->reconciliation = $reconciliation;
    }

    /**
     * Program Context ID
     *
     * @return int | null
     */
    public function getProgramIDAttribute()
    {
        return data_get($this->reconciliation->program, 'id');
    }

    /**
     * Program Context Date
     *
     * @return int | null
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->reconciliation->program->start_date);
    }

    /**
     * Program Context Brand
     *
     * @return string | null
     */
    public function getProgramBrandAttribute()
    {
        return data_get($this->reconciliation->program->primaryBrand, 'label');
    }

    /**
     * Program Context Type
     *
     * @return string | null
     */
    public function getProgramTypeAttribute()
    {
        return $this->reconciliation->program->programTypeLabel;
    }

    /**
     * Yes if the Program is reconciled, otherwise No
     *
     * @return string | null
     */
    public function getReconciledAttribute()
    {
        return $this->boolString($this->reconciliation->program->is_reconciled);
    }

    /**
     * Closeout Initiated Date
     *
     * @return string | null
     */
    public function getCloseoutInitiatedDateAttribute()
    {
        $closeoutInit = $this->reconciliation->isCloseoutInitiatedHistory;
        return excel_date(data_get($closeoutInit, 'created_at'));
    }

    /**
     * CORE Current Status
     *
     * @return string | null
     */
    public function getCORECurrentStatusAttribute()
    {
        $reconciliationHistory = $this->reconciliation->histories->last();
        return $reconciliationHistory->currentStatus;
    }

    /**
     * Closeout Reconciliation Date
     *
     * @return string | null
     */
    public function getCloseoutClaimedByAttribute()
    {
        $closeoutClaimed = $this->reconciliation->isCloseoutInProgressHistory;

        return ($closeoutClaimed) ? data_get($closeoutClaimed->createdBy, 'full_Name') : null;
    }

    /**
     * Closeout Claimed Date
     *
     * @return string | null
     */
    public function getCloseoutClaimedDateAttribute()
    {
       $closeoutClaimed = $this->reconciliation->isCloseoutInProgressHistory;

        return excel_date(data_get($closeoutClaimed, 'created_at'));
    }

    /**
     * Closeout Rejected By
     *
     * @return string | null
     */
    public function getCloseoutRejectedByAttribute()
    {
        $closeoutRejected = $this->reconciliation->isCloseoutRejectedHistory;

        return ($closeoutRejected) ? data_get($closeoutRejected->createdBy, 'full_Name') : null;
    }

    /**
     * Closeout Rejected Date
     *
     * @return string | null
     */
    public function getCloseoutRejectedDateAttribute()
    {
       $closeoutRejected = $this->reconciliation->isCloseoutRejectedHistory;

        return excel_date(data_get($closeoutRejected, 'created_at'));
    }

    /**
     * Closeout Completed By
     *
     * @return string | null
     */
    public function getCloseoutCompletedByAttribute()
    {
        $closeoutCompleted = $this->reconciliation->isCloseoutCompletedHistory;

        return ($closeoutCompleted) ? data_get($closeoutCompleted->createdBy, 'full_Name') : null;
    }

    /**
     * Closeout Completed Date
     *
     * @return string | null
     */
    public function getCloseoutCompletedDateAttribute()
    {
       $closeoutCompleted = $this->reconciliation->isCloseoutCompletedHistory;

        return excel_date(data_get($closeoutCompleted, 'created_at'));
    }

    /**
     * Initial Recon Claimed By
     *
     * @return string | null
     */
    public function getInitialReconClaimedByAttribute()
    {
        $initialReconClaimed = $this->reconciliation->isInitialReconInitiatedHistory;

        return ($initialReconClaimed) ? data_get($initialReconClaimed->createdBy, 'full_Name') : null;
    }

    /**
     * Initial Recon Claimed Date
     *
     * @return string | null
     */
    public function getInitialReconClaimedDateAttribute()
    {
        $initialReconClaimed = $this->reconciliation->isInitialReconInitiatedHistory;

        return excel_date(data_get($initialReconClaimed, 'created_at'));
    }

    /**
     * Initial Recon Rejected By
     *
     * @return string | null
     */
    public function getInitialReconRejectedByAttribute()
    {
        $initialReconRejected = $this->reconciliation->isInitialReconRejectedHistory;

        return ($initialReconRejected) ? data_get($initialReconRejected->createdBy, 'full_Name') : null;
    }

    /**
     * Initial Recon Rejected
     *
     * @return string | null
     */
    public function getInitialReconRejectedDateAttribute()
    {
       $initialReconRejected = $this->reconciliation->isInitialReconRejectedHistory;

        return excel_date(data_get($initialReconRejected, 'created_at'));
    }

    /**
     * Initial Recon Completed By
     *
     * @return string | null
     */
    public function getInitialReconCompletedByAttribute()
    {
        $initialReconCompleted = $this->reconciliation->isInitialReconciliationCompleteHistory;

        return ($initialReconCompleted) ? data_get($initialReconCompleted->createdBy, 'full_Name') : null;
    }

    /**
     * Initial Recon Completed Date
     *
     * @return string | null
     */
    public function getInitialReconCompletedDateAttribute()
    {
        $initialReconCompleted = $this->reconciliation->isInitialReconciliationCompleteHistory;

        return excel_date(data_get($initialReconCompleted, 'created_at'));
    }

    /**
     * Final Recon Claimed By
     *
     * @return string | null
     */
    public function getFinalReconClaimedByAttribute()
    {
        $finalReconClaimed = $this->reconciliation->isFinalReconInProgressHistory;

        return ($finalReconClaimed) ? data_get($finalReconClaimed->createdBy, 'full_Name') : null;
    }

    /**
     * Final Recon Claimed Date
     *
     * @return string | null
     */
    public function getFinalReconClaimedDateAttribute()
    {
        $finalReconClaimed = $this->reconciliation->isFinalReconInProgressHistory;

        return excel_date(data_get($finalReconClaimed, 'created_at'));
    }

    /**
     * Final Recon Completed By
     *
     * @return string | null
     */
    public function getFinalReconCompletedByAttribute()
    {
        $finalReconCompleted = $this->reconciliation->isFinalReconCompleteHistory;

        return ($finalReconCompleted) ? data_get($finalReconCompleted->createdBy, 'full_Name') : null;
    }

    /**
     * Final Recon Completed Date
     *
     * @return string | null
     */
    public function getFinalReconCompletedDateAttribute()
    {
        $finalReconCompleted = $this->reconciliation->isFinalReconCompleteHistory;

        return excel_date(data_get($finalReconCompleted, 'created_at'));
    }

}
