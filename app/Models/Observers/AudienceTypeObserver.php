<?php

namespace Betta\Models\Observers;

use Betta\Models\AudienceType;
use Betta\Foundation\Eloquent\AbstractObserver;

class AudienceTypeObserver extends AbstractObserver
{
    /**
     * Listen to the AudienceType creating event.
     *
     * @param  AudienceType  $model
     * @return void
     */
    public function creating(AudienceType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the AudienceType created event.
     *
     * @param  AudienceType  $model
     * @return void
     */
    public function created(AudienceType $model)
    {

    }
}
