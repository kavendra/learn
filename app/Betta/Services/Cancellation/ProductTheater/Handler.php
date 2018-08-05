<?php

namespace Betta\Services\Cancellation\ProductTheater;

use Carbon\Carbon;
use Betta\Models\Program;
use Betta\Cancellation\Handlers\BaseHandler;

class Handler extends BaseHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Time distance for the late cancellation
     *
     * @var integer
     */
    protected $late;

    /**
     * Set the comparator
     *
     * @var string
     */
    protected $comparator;

    /**
     * Set the Program
     *
     * @param Program $program
     * @return $this
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Resolve the ID of the cost to use for the cancellation
     *
     * @return int
     */
    public function costTypeId()
    {
        return $this->getFeeHandler()->type();
    }

    /**
     * Resolve actual Cost
     *
     * @return Betta\Models\Cost | null
     */
    public function cost()
    {
        return $this->program->costs->where('cost_id', $this->costTypeId())->first();
    }

    /**
     * Resolve the value of the cancellation fee
     *
     * @return float
     */
    public function fee()
    {
        return $this->getFeeHandler()->value();
    }

    /**
     * True if the cancellation is late
     *
     * @return boolean
     */
    public function isLate()
    {
        $method = camel_case("diff_in_{$this->comparator}");

        return $this->program->start_date->{$method}($this->at, false) >= $this->late;
    }

    /**
     * True if the cost already exits
     *
     * @return boolean
     */
    public function costExists()
    {
        return $this->program->costs->where('cost_id', $this->costType())->isNotEmpty();
    }

    /**
     * Verbose cancellation message
     *
     * @return string
     */
    public function render()
    {
        return vsprintf('Program ID %s (%s) set for %s is a %s on %s, calculated as %s', [
            $this->program->id,
            $this->program->full_label,
            $this->program->start_date->format(config('betta.short_date')),
            $this->isLate() ? trans('cancellation::program.late_cancellation') : trans('cancellation::program.regular_cancellation'),
             $this->at->format(config('betta.short_date')),
            $this->getFeeHandler()->render()
        ]);
    }

    /**
     * Get Fee Handler
     *
     * @return Betta\Cancellation\Handlers\Program\Fee
     */
    protected function getFeeHandler($reset = false)
    {
        return empty($this->feeHandler) ? $this->feeHandler = $this->setFeehandler() : $this->feeHandler;
    }

    /**
     * Set the Fee Handler
     *
     * @return Betta\Cancellation\Handlers\Program\Fee
     */
    protected function setFeeHandler()
    {
        return $this->isLate()
             ? new LateFee($this->program)
             : new CancellationFee($this->program);
    }
}
