<?php

namespace Betta\Models\Observers;

use Betta\Models\Address;
use Betta\Foundation\Eloquent\AbstractObserver;

class AddressObserver extends AbstractObserver
{
    /**
     * Listen to the Address creating event.
     *
     * @param  Address  $model
     * @return void
     */
    public function creating(Address $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # try to guess the label of the owner
        $model->setAttribute('location_name', $model->getAttribute('location_name') ?: $this->getOwnerLabel($model) );
    }

    /**
     * Listen to the Address created event.
     *
     * @param  Address  $profile
     * @return void
     */
    public function created(Address $model)
    {
        # we can queue job to find out location_id from Google
    }


    /**
     * Listen to the Address saving event.
     *
     * @param  Address  $model
     * @return void
     */
    public function saving(Address $model)
    {
        # Clean up the Cell Phone
        $model->setAttribute('phone', $this->numbersOnly($model->getAttribute('phone')) );

        # Clean up the Phone
        $model->setAttribute('cell_phone', $this->numbersOnly($model->getAttribute('cell_phone')) );

        # Clean up the Fax
        $model->setAttribute('fax', $this->numbersOnly($model->getAttribute('fax')) );
    }


    /**
     * Try to guess the label of the location
     *
     * @todo  A Good candidate for the AddressLabelInterface that will provide the uniform means of getting the label for the address
     * @param  Address $model
     * @return string
     */
    protected function getOwnerLabel(Address $model)
    {
        return object_get($model->owner, 'label');
    }
}
