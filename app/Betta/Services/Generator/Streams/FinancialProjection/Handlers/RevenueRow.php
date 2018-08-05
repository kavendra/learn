<?php

namespace Betta\Services\Generator\Streams\FinancialProjection\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RevenueRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Bind the implementation
     * This attributes will be used to return the sum of the costs
     * @var Array
     */
    protected $cost_attributes = [
        'Management Fee',
        'SP Check Processing Fee',
        'Attendee Closeout Fee',
        'Materials Fulfilment Fee',
        'Other Fee',
    ];

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'ID',
        'Status',
        'Date',
        'Type',
        'Management Fee',
        'SP Check Processing Fee',
        'Attendee Closeout Fee',
        'Materials Fullfillment Fee',
        'Other Fee',
        'Total Program Cost',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
    ];

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
     * Get Program Id
     *
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Program Status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Get Program start date
     *
     * @return float
     */
    public function getDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Program Type
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return data_get($this->program, 'programType.label');
    }

    /**
     * Get Management Fee
     *
     * @return float
     */
    public function getManagementFeeAttribute()
    {
        return $this->program->base_fee_category_costs->sum('real');
    }

    /**
     * Get SP Check Processing Fee
     *
     * @return float
     */
    public function getSPCheckProcessingFeeAttribute()
    {
        return $this->program->check_processing_costs->sum('real');
    }

    /**
     * Get Attendee Closeout Fee
     *
     * @return float
     */
    public function getAttendeeCloseoutFeeAttribute()
    {
        return $this->program->attendee_closeout_costs->sum('real');
    }

    /**
     * Get Materials Fullfillment Fee
     *
     * @return float
     */
    public function getMaterialsFulfilmentFeeAttribute()
    {
        return $this->program->program_materials_fulfillment_costs->sum('real');
    }

    /**
     * Get Other Fee
     *
     * @return float
     */
    public function getOtherFeeAttribute()
    {
        return $this->program->other_fee_invoice_costs->sum('real');
    }

    /**
     * Get Total Program Cost
     *
     * @return float
     */
    public function getTotalProgramCostAttribute()
    {
        return array_sum( array_only($this->attributes, $this->cost_attributes) );

    }
}
