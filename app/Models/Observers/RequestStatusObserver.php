<?php

namespace Betta\Models\Observers;

use Betta\Models\RequestStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestStatusObserver extends AbstractObserver
{
    /**
     * Listen to the RequestStatus creating event.
     *
     * @param  RequestStatus  $model
     * @return void
     */
    public function creating(RequestStatus $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
