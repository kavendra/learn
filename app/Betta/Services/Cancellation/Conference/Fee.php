<?php

namespace Betta\Services\Cancellation\Conference;

use Betta\Models\Conference;
use Betta\Models\CostItem;

abstract class Fee
{
    use LevelOfWork;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conference;

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
     * @param Conference $conference
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
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
        return $this->default_cost_id;
    }

    /**
     * Render method to tell how we arrived to the value
     *
     * @return string
     */
    public function render()
    {
        if($this->confirmedSpeakerAndVenue()){
            $completed = trans('cancellation::conference.confirmed_speaker_venue');
        } elseif($this->confirmedSpeaker()){
            $completed = trans('cancellation::conference.confirmed_speaker_venue');
        } elseif($this->initiated()) {
            $completed = trans('cancellation::conference.reviewed');
        } else {
            $completed = '';
        }
        return vsprintf('Cost ID %d of $%s x %s (%s) = $%s',[
            $this->type(),
            number_format($this->cost(), 2),
            $this->factor(),
            $completed,
            number_format($this->value(),2)
        ]);
    }

    /**
     * Return cost value based on Scope of Work
     *
     * @return float
     */
    protected function cost()
    {
        return $this->baseCost();
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
        # check maximum first
        if($this->confirmedSpeakerAndVenue()){
            return 0.75;
        }

        if($this->confirmedSpeaker()){
            return 0.5;
        }

        if($this->initiated()){
            return 0.3;
        }

        return 0;
    }
}
