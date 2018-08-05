<?php

namespace Betta\Models\Observers;

use Betta\Models\PaymentType;
use Betta\Foundation\Eloquent\AbstractObserver;

class PaymentTypeObserver extends AbstractObserver
{
    /**
     * Listen to the PaymentType creating event.
     *
     * @param  PaymentType  $model
     * @return void
     */
    public function creating(PaymentType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the PaymentType created event.
     *
     * @param  PaymentType  $model
     * @return void
     */
    public function created(PaymentType $model)
    {

    }
}
