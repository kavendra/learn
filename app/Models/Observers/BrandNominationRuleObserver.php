<?php

namespace Betta\Models\Observers;

use Betta\Models\BrandNominationRule;
use Betta\Foundation\Eloquent\AbstractObserver;

class BrandNominationRuleObserver extends AbstractObserver
{
    /**
     * Listen to the BrandNominationRule creating event.
     *
     * @param  BrandNominationRule  $model
     * @return void
     */
    public function creating(BrandNominationRule $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

    }

    /**
     * Listen to the BrandNominationRule created event.
     *
     * @param  BrandNominationRule  $model
     * @return void
     */
    public function created(BrandNominationRule $model)
    {

    }
}
