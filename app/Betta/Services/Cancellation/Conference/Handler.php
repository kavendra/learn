<?php

namespace Betta\Services\Cancellation\Conference;

use Carbon\Carbon;
use Betta\Models\Conference;
use Betta\Cancellation\Handlers\BaseHandler;

class Handler extends BaseHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conference;

    /**
     * Set the Conference
     *
     * @param Conference $conference
     * @return $this
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
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
        return $this->conference->costs->where('cost_id', $this->costTypeId())->first();
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
     * @param Carbon\Carbon | null $at
     * @return boolean
     */
    public function isLate($at = null)
    {
        $at = $this->at($at);

        return $this->conference->exibitor_start_date->gt($at)
           and $this->conference->exibitor_start_date->diffInDays($at) <= $this->late;
    }

    /**
     * True if the cost already exits
     *
     * @return boolean
     */
    public function costExists()
    {
        return $this->conference->costs->where('cost_id', $this->costType())->isNotEmpty();
    }

    /**
     * Verbose cancellation message
     *
     * @return string
     */
    public function render()
    {
        return vsprintf('Conference ID %s (%s) set for %s need a %s, calculated as %s', [
            $this->conference->id,
            $this->conference->label,
            $this->conference->exibitor_start_date->format(config('betta.short_date')),
            $this->isLate()
                ? trans('cancellation::conference.late_cancellation')
                : trans('cancellation::conference.regular_cancellation'),
            $this->getFeeHandler()->render()
        ]);
    }

    /**
     * Get Fee Handler
     *
     * @return Betta\Cancellation\Handlers\Conference\Fee
     */
    protected function getFeeHandler($reset = false)
    {
        return empty($this->feeHandler) ? $this->feeHandler = $this->setFeehandler() : $this->feeHandler;
    }

    /**
     * Set the Fee Handler
     *
     * @return Betta\Cancellation\Handlers\Conference\Fee
     */
    protected function setFeeHandler()
    {
        return $this->isLate()
             ? new LateFee($this->conference)
             : new CancellationFee($this->conference);
    }
}
