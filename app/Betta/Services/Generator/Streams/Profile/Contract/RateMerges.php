<?php

namespace Betta\Services\Generator\Streams\Profile\Contract;

use Betta\Models\ProfileRateCard;

trait RateMerges
{
    /**
     * Get Rate: audio
     *
     * @return string
     */
    public function getTeleconferenceAttribute()
    {
        return $this->firstRate($this->rateCard,'teleconference_rate');
    }

    /**
     * Get Rate: audio
     *
     * @return string
     */
    public function getAudioAttribute()
    {
        return $this->getTeleconferenceAttribute();
    }

    /**
     * Get Rate: training
     *
     * @return string
     */
    public function getTrainingAttribute()
    {
        return $this->firstRate($this->rateCard,'training_rate');
    }

    /**
     * Get Rate: congress_activity
     *
     * @return string
     */
    public function getCongressActivityAttribute()
    {
        return $this->firstRate($this->rateCard,'congress_activity');
    }

    /**
     * Get first_rate_up_200
     *
     * @return string
     */
    public function getFirstRateUp200Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get first_rate_up_1000
     *
     * @return string
     */
    public function getFirstRateUp1000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get first_rate_up_3000
     *
     * @return string
     */
    public function getFirstRateUp3000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get first_rate_up_7000
     *
     * @return string
     */
    public function getFirstRateUp7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get first_rate_over_7000
     *
     * @return string
     */
    public function getFirstRateOver7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get multi_rate2_up_200
     *
     * @return string
     */
    public function getMultiRate2Up200Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get multi_rate2_up_1000
     *
     * @return string
     */
    public function getMultiRate2Up1000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get multi_rate2_up_3000
     *
     * @return string
     */
    public function getMultiRate2Up3000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get multi_rate2_up_7000
     *
     * @return string
     */
    public function getMultiRate2Up7000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get multi_rate2_over_7000
     *
     * @return string
     */
    public function getMultiRate2Over7000Attribute()
    {
        return $this->secondRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get multi_rate3_up_200
     *
     * @return string
     */
    public function getMultiRate3Up200Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up200');
    }

    /**
     * Get multi_rate3_up_1000
     *
     * @return string
     */
    public function getMultiRate3Up1000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up1000');
    }

    /**
     * Get multi_rate3_up_3000
     *
     * @return string
     */
    public function getMultiRate3Up3000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up3000');
    }

    /**
     * Get multi_rate3_up_7000
     *
     * @return string
     */
    public function getMultiRate3Up7000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_up7000');
    }

    /**
     * Get multi_rate3_over_7000
     *
     * @return string
     */
    public function getMultiRate3Over7000Attribute()
    {
        return $this->thirdRate($this->rateCard, 'rate_over7000');
    }

    /**
     * Get live_training_rate_up_200
     *
     * @return string
     */
    public function getLiveTrainingRateUp200Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up200');
    }

    /**
     * Get live_training_rate_up_1000
     *
     * @return string
     */
    public function getLiveTrainingRateUp1000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up1000');
    }

    /**
     * Get live_training_rate_up_3000
     *
     * @return string
     */
    public function getLiveTrainingRateUp3000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up3000');
    }

    /**
     * Get live_training_rate_up_7000
     *
     * @return string
     */
    public function getLiveTrainingRateUp7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_up7000');
    }

    /**
     * Get live_training_rate_over_7000
     *
     * @return string
     */
    public function getLiveTrainingRateOver7000Attribute()
    {
        return $this->firstRate($this->rateCard, 'live_training_rate_over7000');
    }

    /**
     * Get the First rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function firstRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $value = data_get($card, "{$type}.honorarium_rate_1", 0);

        return $formatted ? $this->format($value) : $value;
    }

    /**
     * Get the second rate
     *
     * @param  ProfileRateCard $card
     * @return SUM two rates
     */
    protected function secondRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $value = data_get($card, "{$type}.honorarium_rate_1", 0)
               + data_get($card, "{$type}.honorarium_rate_2", 0);

        return $formatted ? $this->format($value) : $value;
    }

    /**
     * Get the third rate
     *
     * @return SUM three rates
     */
    protected function thirdRate(ProfileRateCard $card, $type='rate_up200', $formatted = true)
    {
        $value = $this->secondRate($card, $type, false)
               + data_get($card, "{$type}.honorarium_rate_3", 0);

        return $formatted ? $this->format($value) : $value;
    }

    /**
     * Format the rate
     *
     * @param  mixed $value
     * @return string
     */
    protected function format($value)
    {
        return $this->currency.' '.number_format($value, 2);
    }
}
