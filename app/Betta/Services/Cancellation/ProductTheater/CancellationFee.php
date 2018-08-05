<?php

namespace Betta\Services\Cancellation\ProductTheater;

use Betta\Models\Program;
use Betta\Models\CostItem;

class CancellationFee extends Fee
{
    /**
     * Cost ID to use by default
     *
     * @var int
     */
    protected $default_cost_id = CostItem::CANCELLATION_FEE;

    /**
     * Fee type to look for
     *
     * @var string
     */
    protected $fee_type = 'cancellation_fee';
}
