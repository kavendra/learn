<?php

namespace Betta\Models\Observers;

use Betta\Models\AssessmentType;
use Betta\Foundation\Eloquent\AbstractObserver;

class AssessmentTypeObserver extends AbstractObserver
{
    /**
     * Listen to the AssessmentType creating event.
     *
     * @param  AssessmentType  $model
     * @return void
     */
    public function creating(AssessmentType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
