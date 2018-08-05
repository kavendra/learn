<?php

namespace Betta\Models\Observers;

use Betta\Models\RequestCloseout as Model;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestCloseoutObserver extends AbstractObserver
{
    /**
     * Listen to the RequestCloseout creating event.
     *
     * @param  RequestCloseout  $model
     * @return void
     */
    public function creating(Model $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
