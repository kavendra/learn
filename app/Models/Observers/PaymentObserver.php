<?php

namespace Betta\Models\Observers;

use Betta\Models\Payment;
use Betta\Foundation\Eloquent\AbstractObserver;

class PaymentObserver extends AbstractObserver
{
    /**
     * Listen to the Payment creating event.
     *
     * @param  Payment  $model
     * @return void
     */
    public function creating(Payment $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
