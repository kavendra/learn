<?php

namespace Betta\Models\Observers;

use Betta\Models\TrainingCourse;
use Betta\Foundation\Eloquent\AbstractObserver;

class TrainingCourseObserver extends AbstractObserver
{
    /**
     * Listen to the TrainingCourse creating event.
     *
     * @param  TrainingCourse  $model
     * @return void
     */
    public function creating(TrainingCourse $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the TrainingCourse created event.
     *
     * @param  TrainingCourse  $model
     * @return void
     */
    public function created(TrainingCourse $model)
    {

    }
}
