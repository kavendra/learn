<?php

namespace Betta\Models\Observers;

use Betta\Models\RequestHistory as Model;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the Model creating event.
     *
     * @param  Model  $model
     * @return void
     */
    public function creating(Model $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
