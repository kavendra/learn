<?php

namespace Betta\Models\Observers;

use Betta\Models\VendorType;
use Betta\Foundation\Eloquent\AbstractObserver;

class VendorTypeObserver extends AbstractObserver
{
    /**
     * Listen to the VendorType creating event.
     *
     * @param  VendorType  $model
     * @return void
     */
    public function creating(VendorType $model)
    {
        # Produce Reference Name
        $model->setAttribute('reference_name', $model->getAttribute('reference_name') ?: $this->uniqueReferenceName($model) );

        # Ensure ucfirst()
        $model->setAttribute('label', ucfirst($model->getAttribute('label')) );
    }


    /**
     * Make a Reference Name
     *
     * @param  VendorType $model
     * @return string
     */
    protected function uniqueReferenceName(VendorType $model)
    {
        return $this->uniqueField('reference_name', snake_case($model->label));
    }
}
