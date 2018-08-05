<?php

namespace Betta\Models\Observers;

use Betta\Models\EngagementHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class EngagementHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the EngagementHistory creating event.
     *
     * @param  EngagementHistory  $model
     * @return void
     */
    public function creating(EngagementHistory $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId());
    }
}
