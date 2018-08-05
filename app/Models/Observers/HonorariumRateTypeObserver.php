<?php

namespace Betta\Models\Observers;

use Betta\Models\HonorariumRateType;
use Betta\Foundation\Eloquent\AbstractObserver;

class HonorariumRateTypeObserver extends AbstractObserver
{
    /**
     * Listen to the HonorariumRateType creating event.
     *
     * @param  HonorariumRateType  $model
     * @return void
     */
    public function creating(HonorariumRateType $model)
    {
        # if the Reference Name if is not provided
        $model->setAttribute('reference_name', $model->getAttribute('reference_name') ?: $this->uniqueReferenceName($model) );
    }


    /**
     * Make a Reference Name
     *
     * @param  HonorariumRateType $model
     * @return string
     */
    protected function uniqueReferenceName(HonorariumRateType $model)
    {
        return $this->uniqueField('reference_name', snake_case($model->label));
    }
}
