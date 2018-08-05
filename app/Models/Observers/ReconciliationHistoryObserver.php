<?php

namespace Betta\Models\Observers;

use Betta\Models\ReconciliationHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ReconciliationHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ReconciliationHistory creating event.
     *
     * @param  ReconciliationHistory  $model
     * @return void
     */
    public function creating(ReconciliationHistory $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
