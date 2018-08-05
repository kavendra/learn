<?php

namespace Betta\Services\Cancellation\ProductTheater;

use Betta\Models\Program;
use Betta\Models\CostItem;

class LateFee extends Fee
{
    /**
     * Cost ID to use by default
     *
     * @var int
     */
    protected $default_cost_id = CostItem::LATE_CANCELLATION;

    /**
     * Fee type to look for
     *
     * @var string
     */
    protected $fee_type = 'late_cancellation_fee';

    /**
     * Return multiplication factor based on Level of Work
     *
     * @return float
     */
    protected function factor()
    {
        if ($this->confirmedSpeaker()) {
            return 1;
        }

        if ($this->initiated()) {
            return 1;
        }

        return 0;
    }
}
