<?php

namespace Betta\Models\Observers;

use Betta\Models\PaymentStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class PaymentStatusObserver extends AbstractObserver
{
    /**
     * Listen to the PaymentStatus creating event.
     *
     * @param  PaymentStatus  $model
     * @return void
     */
    public function creating(PaymentStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }


     /**
     * Listen to the Tier created event.
     *
     * @param  PaymentStatus  $model
     * @return void
     */
    public function created(PaymentStatus $model)
    {

    }
}
