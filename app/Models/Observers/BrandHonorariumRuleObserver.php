<?php

namespace Betta\Models\Observers;

use Betta\Models\BrandHonorariumRule;
use Betta\Foundation\Eloquent\AbstractObserver;

class BrandHonorariumRuleObserver extends AbstractObserver
{
    /**
     * Listen to the BrandHonorariumRule creating event.
     *
     * @param  BrandHonorariumRule  $model
     * @return void
     */
    public function creating(BrandHonorariumRule $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

    }

    /**
     * Listen to the BrandHonorariumRule created event.
     *
     * @param  BrandHonorariumRule  $model
     * @return void
     */
    public function created(BrandHonorariumRule $model)
    {

    }
}
