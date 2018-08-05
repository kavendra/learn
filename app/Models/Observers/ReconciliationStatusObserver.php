<?php

namespace Betta\Models\Observers;

use Betta\Models\ReconciliationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ReconciliationStatusObserver extends AbstractObserver
{
    /**
     * Listen to the ReconciliationStatus creating event.
     *
     * @param  ReconciliationStatus  $model
     * @return void
     */
    public function creating(ReconciliationStatus $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
