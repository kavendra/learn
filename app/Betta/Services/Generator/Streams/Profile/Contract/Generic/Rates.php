<?php

namespace Betta\Services\Generator\Streams\Profile\Contract\Generic;

use Carbon\Carbon;
use Betta\Models\Contract;
use Betta\Models\ProfileRateCard;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

trait Rates
{
    /**
     * Get Audio
     *
     * @return string
     */
    public function getAudioAttribute()
    {
        return $this->firstRate($this->rateCard,'teleconference_rate');
    }

    /**
     * Get Rate for: Ad Board Chair
     *
     * @return string
     */
    public function getRateAdBoardChairAttribute()
    {
        return $this->firstRate($this->rateCard,'ad_board_chair_rate');
    }

    /**
     * Get Rate for: Ad Board Member
     *
     * @todo    Dormant functionality
     * @return string
     */
    public function getRateAdBoardMemberAttribute()
    {
        return $this->firstRate($this->rateCard,'ad_board_member_rate');
    }

    /**
     * Get Training
     *
     * @return string
     */
    public function getTrainingAttribute()
    {
        return $this->firstRate($this->rateCard,'training_rate');
    }

    /**
     * Get Congress Activity
     *
     * @return string
     */
    public function getCongressActivityAttribute()
    {
        return $this->firstRate($this->rateCard,'congress_activity');
    }

    /**
     * Get First Rate Up 200
     *
     * @return string
     */
    public function getFirstRateUp200Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get First Rate Up 1000
     *
     * @return string
     */
    public function getFirstRateUp1000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get First Rate Up 3000
     *
     * @return string
     */
    public function getFirstRateUp3000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get First Rate Up 7000
     *
     * @return string
     */
    public function getFirstRateUp7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get First Rate Over 7000
     *
     * @return string
     */
    public function getFirstRateOver7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get Multi Rate2 Up 200
     *
     * @return string
     */
    public function getMultiRate2Up200Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get Multi Rate2 Up 1000
     *
     * @return string
     */
    public function getMultiRate2Up1000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get Multi Rate2 Up 3000
     *
     * @return string
     */
    public function getMultiRate2Up3000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get Multi Rate2 Up 7000
     *
     * @return string
     */
    public function getMultiRate2Up7000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get Multi Rate2 Over 7000
     *
     * @return string
     */
    public function getMultiRate2Over7000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get Multi Rate3 Up 200
     *
     * @return string
     */
    public function getMultiRate3Up200Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get Multi Rate3 Up 1000
     *
     * @return string
     */
    public function getMultiRate3Up1000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get Multi Rate3 Up 3000
     *
     * @return string
     */
    public function getMultiRate3Up3000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get Multi Rate3 Up 7000
     *
     * @return string
     */
    public function getMultiRate3Up7000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get Multi Rate3 Over 7000
     *
     * @return string
     */
    public function getMultiRate3Over7000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get Live Training Rate Up 200
     *
     * @return string
     */
    public function getLiveTrainingRateUp200Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up200');
    }

    /**
     * Get Live Training Rate Up 1000
     *
     * @return string
     */
    public function getLiveTrainingRateUp1000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up1000');
    }

    /**
     * Get Live Training Rate Up 3000
     *
     * @return string
     */
    public function getLiveTrainingRateUp3000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up3000');
    }

    /**
     * Get Live Training Rate Up 7000
     *
     * @return string
     */
    public function getLiveTrainingRateUp7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up7000');
    }

    /**
     * Get Live Training Rate Over 7000
     *
     * @return string
     */
    public function getLiveTrainingRateOver7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_over7000');
    }

    /**
     * Resolve last Max Cap applicable
     *
     * @access private
     * @return MaxCap | null
     */
    public function getMaxCapAttribute()
    {
        return $this->contract->maxcaps->last();
    }

    /**
     * Resolve Max Cap limit
     *
     * @return float | null
     */
    public function getMaxCapLimitAttribute()
    {
        return number_format(data_get($this->maxCap, 'honorarium_limit'), 2);
    }

    /**
     * Verbose Max Cap limit
     *
     * @return float | null
     */
    public function getMaxCapLimitVerboseAttribute()
    {
        $value = verbalize(data_get($this->maxCap, 'honorarium_limit'));

        return ucwords(title_case($value));
    }

    /**
     * Resolve Max Cap limit
     *
     * @return float | null
     */
    public function getMaxCapThresholdAttribute()
    {
        return number_format(data_get($this->maxCap, 'threshold'), 2);
    }

    /**
     * Verbose  Max Cap limit
     *
     * @return float | null
     */
    public function getMaxCapThresholdVerboseAttribute()
    {
        $value = verbalize(data_get($this->maxCap, 'threshold'));

        return ucwords(title_case($value));
    }

    /**
     * Get the First rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function firstRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        # Calculate
        $val = data_get($card, "{$type}.honorarium_rate_1", 0);
        # Return (un)formatted
        return $formatted ? $this->format($val) : $val;
    }

    /**
     * Get the second rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function secondRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        # Calculate
        $val = data_get($card, "{$type}.honorarium_rate_1", 0)
             + data_get($card, "{$type}.honorarium_rate_2", 0);
        # Return (un)formatted
        return $formatted ? $this->format($val) : $val;
    }


    /**
     * Get the third rate
     *
     * @return SUM three rates
     */
    protected function thirdRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        # Calculate
        $val = $this->secondRate($card, $type, false)
             + data_get($card, "{$type}.honorarium_rate_3", 0);
        # Return (un)formatted
        return $formatted ? $this->format($val) : $val;
    }

    /**
     * Format the rate
     *
     * @param  mixed $value
     * @return string
     */
    protected function format($value)
    {
        return '$ '. number_format($value, 2);
    }
}
