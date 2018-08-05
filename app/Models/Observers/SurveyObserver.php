<?php

namespace Betta\Models\Observers;

use Betta\Models\Survey;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(Survey $model)
    {
       $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(Survey $model)
    {

    }
}
