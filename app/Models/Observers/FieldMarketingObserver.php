<?php

namespace Betta\Models\Observers;

use Betta\Models\FieldMarketing;
use Betta\Foundation\Eloquent\AbstractObserver;

class FieldMarketingObserver extends AbstractObserver
{
    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(FieldMarketing $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

       
    }

    
}
