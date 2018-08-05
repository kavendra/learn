<?php

namespace Betta\Models\Observers;

use Betta\Models\AddressContact;
use Betta\Foundation\Eloquent\AbstractObserver;

class AddressContactObserver extends AbstractObserver
{
    /**
     * Listen to the AddressContact creating event.
     *
     * @param  AddressContact  $model
     * @return void
     */
    public function creating(AddressContact $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the AddressContact created event.
     *
     * @param  AddressContact  $model
     * @return void
     */
    public function created(AddressContact $model)
    {

    }
}
