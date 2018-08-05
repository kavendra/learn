<?php

namespace Betta\Models\Observers;

use Betta\Models\EngagementStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class EngagementStatusObserver extends AbstractObserver
{
    /**
     * Listen to the EngagementStatus creating event.
     *
     * @param  EngagementStatus  $model
     * @return void
     */
    public function creating(EngagementStatus $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId());
    }
}
