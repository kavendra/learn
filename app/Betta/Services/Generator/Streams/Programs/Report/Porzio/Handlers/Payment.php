<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait Payment
{
    /**
     * Enumerated value for the Number of Payments
     *
     * @return string
     */
    public function getNumberOfPaymentsReflectedAttribute()
    {
        return 1;
    }

    /**
     * Reserved for Future Use
     *
     * @return void
     */
    public function getPaymentContextualInformationAttribute()
    {
        return;
    }
}
