<?php

namespace Betta\Services\Generator\Streams\Programs\Report\ManagementFee\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ProgramManagementFeeRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Program Date',
        'Label',
        'Primary Brand',
        'Program Type',
        'Account Manager',
        'Base Fee - Estimate',
        'Meeting Materials - Estimate',
        'Change Fee - Estimate',
        'Attendee Closeout - Estimate',
        'Expedited Fee - Estimate',
        'Convinience Fee - Estimate',
        'Cancellation Fee - Estimate',
        'Late Cancellation Fee - Estimate',
        'Attendee Closeout Convenience - Estimate',
        'Base Fee - Actual',
        'Meeting Materials - Actual',
        'Change Fee - Actual',
        'Attendee Closeout - Actual',
        'Expedited Fee - Actual',
        'Convinience Fee - Actual',
        'Cancellation Fee - Actual',
        'Late Cancellation Fee - Actual',
        'Attendee Closeout Convenience - Actual',
        'Check Processing Fee - Actual',
        'Base Fee - Reconciled',
        'Meeting Materials - Reconciled',
        'Change Fee - Reconciled',
        'Attendee Closeout - Reconciled',
        'Expedited Fee - Reconciled',
        'Convinience Fee - Reconciled',
        'Cancellation Fee - Reconciled',
        'Late Cancellation Fee - Reconciled',
        'Attendee Closeout Convenience - Reconciled',
        'Check Processing Fee - Reconciled',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Program ID
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Program Start Date
     *
     * @return string
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Program Label
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        return $this->program->label;
    }

    /**
     * Program brand
     *
     * @return string
     */
    public function getPrimaryBrandAttribute()
    {
        return data_get($this->program,'primary_brand.label');
    }

    /**
     * Program type
     *
     * @return string | null
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Account Manager
     *
     * @return string
     */
    public function getAccountManagerAttribute()
    {
        return data_get($this->program->primary_field, 'preferred_name');
    }

    /**
     * Program Manager Name
     *
     * @return string
     */
    public function getProgramManagerAttribute()
    {
        return data_get($this->program->primary_pm, 'preferred_name');
    }

    /**
     * Base Fee Estimate
     *
     * @return string
     */
    public function getBaseFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getMeetingMaterialsEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getChangeFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getExpeditedFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getConvinienceFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getCancellationFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getLateCancellationFeeEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutConvenienceEstimateAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getBaseFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getMeetingMaterialsActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getChangeFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getExpeditedFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getConvinienceFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getCancellationFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getLateCancellationFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutConvenienceActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getCheckProcessingFeeActualAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getBaseFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getMeetingMaterialsReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getChangeFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getExpeditedFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getConvinienceFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getCancellationFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getLateCancellationFeeReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getAttendeeCloseoutConvenienceReconciledAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future user
     *
     * @return null
     */
    public function getCheckProcessingFeeReconciledAttribute()
    {
        return null;
    }
}
