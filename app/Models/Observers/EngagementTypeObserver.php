<?php

namespace Betta\Models\Observers;

use Betta\Models\EngagementType;
use Betta\Foundation\Eloquent\AbstractObserver;

class EngagementTypeObserver extends AbstractObserver
{
    /**
     * Listen to the EngagementType creating event.
     *
     * @param  EngagementType  $model
     * @return void
     */
    public function creating(EngagementType $model)
    {
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId());
    }
}
