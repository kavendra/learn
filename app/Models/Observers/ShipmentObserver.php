<?php

namespace Betta\Models\Observers;

use Betta\Models\Shipment;
use Betta\Foundation\Eloquent\AbstractObserver;

class ShipmentObserver extends AbstractObserver
{
    /**
     * Listen to the Shipment creating event.
     *
     * @param  Shipment  $model
     * @return void
     */
    public function creating(Shipment $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Shipment created event.
     *
     * @param  Shipment  $model
     * @return void
     */
    public function created(Shipment $model)
    {

    }
}
