<?php

namespace Betta\Models\Observers;

use Betta\Models\Assessment;
use Betta\Foundation\Eloquent\AbstractObserver;

class AssessmentObserver extends AbstractObserver
{
    /**
     * Listen to the Assessment creating event.
     *
     * @param  Assessment  $model
     * @return void
     */
    public function creating(Assessment $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
