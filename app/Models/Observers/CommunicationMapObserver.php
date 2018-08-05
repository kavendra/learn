<?php

namespace Betta\Models\Observers;

use Betta\Models\CommunicationMap;
use Betta\Foundation\Eloquent\AbstractObserver;

class CommunicationMapObserver extends AbstractObserver
{
    /**
     * Listen to the CommunicationMap creating event.
     *
     * @param  CommunicationMap  $model
     * @return void
     */
    public function creating(CommunicationMap $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
