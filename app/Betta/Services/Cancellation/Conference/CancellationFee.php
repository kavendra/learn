<?php

namespace Betta\Services\Cancellation\Conference;

use Betta\Models\Conference;
use Betta\Models\CostItem;

class CancellationFee extends Fee
{
    /**
     * Cost ID to use by default
     *
     * @var int
     */
    protected $default_cost_id = CostItem::CANCELLATION_FEE_CONFERENCES;

    /**
     * Fee type to look for
     *
     * @var string
     */
    protected $fee_type = 'cancellation_fee';
}
