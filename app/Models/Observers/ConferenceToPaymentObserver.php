<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceToPayment;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceToPaymentObserver extends AbstractObserver
{
    
   

    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(ConferenceToPayment $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
	}

   
}
