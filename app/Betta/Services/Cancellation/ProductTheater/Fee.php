<?php

namespace Betta\Services\Cancellation\ProductTheater;

use Betta\Models\Program;
use Betta\Models\CostItem;

abstract class Fee
{
    use ScopeOfWork;
    use LevelOfWork;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Cost ID to use by default
     *
     * @var int
     */
    protected $default_cost_id;

    /**
     * Fee type to look for
     *
     * @var string
     */
    protected $fee_type;

    /**
     * Create new Fee Instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * What shall be the value of the cancellation?
     *
     * @return int
     */
    public function value()
    {
        return $this->factor() * $this->cost();
    }

    /**
     * What Cost Item to use?
     *
     * @return int
     */
    public function type()
    {
        return $this->program->getBusinessRule("{$this->fee_type}_cost_id", $this->default_cost_id);
    }

    /**
     * Render method to tell how we arrived to the value
     *
     * @return string
     */
    public function render()
    {
        if ($this->confirmedSpeaker()) {
            $completed = 'confirmed speaker and conference line';
        } elseif ($this->initiated()) {
            $completed = trans('cancellation::program.reviewed');
        } else {
            $completed = '';
        }
        return vsprintf('Cost ID %d (%s) $%s x %s (%s) = $%s', [
            $this->type(),
            $this->limitedScope() ? 'no meals' : 'with meals',
            number_format($this->cost(), 2),
            $this->factor(),
            $completed,
            number_format($this->value(), 2)
        ]);
    }

    /**
     * Return cost value based on Scope of Work
     *
     * @return float
     */
    protected function cost()
    {
        # get the rule
        $rule = $this->limitedScope() ? 'limited' : 'full';
        # reolve the fule value
        return $this->program->getBusinessRule("{$this->fee_type}_{$rule}", $this->baseCost());
    }

    /**
     * Resolve the base cost value
     *
     * @return float | null
     * @throws  ModelNotFoundException
     */
    protected function baseCost()
    {
        return CostItem::findOrFail($this->type())->cost;
    }

    /**
     * Return multiplication factor based on Level of Work
     *
     * @return float
     */
    protected function factor()
    {
        # We cannot confirm speakers without the audio line, becuase it is always confirmed
        if ($this->confirmedSpeaker() AND $this->confirmedAudioLine()) {
            return 0.75;
        }

        if ($this->confirmedSpeaker()) {
            return 0.75;
        }

        if ($this->initiated()) {
            return 0.3;
        }

        return 0;
    }
}
