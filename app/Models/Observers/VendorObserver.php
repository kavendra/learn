<?php

namespace Betta\Models\Observers;

use Betta\Models\Vendor;
use Betta\Foundation\Eloquent\AbstractObserver;

class VendorObserver extends AbstractObserver
{
    /**
     * Listen to the Vendor creating event.
     *
     * @param  Vendor  $model
     * @return void
     */
    public function creating(Vendor $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Clean up the Contact Phone
        $model->setAttribute('contact_phone', $this->numbersOnly($model->getAttribute('contact_phone')) );
    }
}
