<?php

namespace Betta\Models\Observers;

use Betta\Models\SurveyMap;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyMapObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(SurveyMap $model)
    {
       $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(SurveyMap $model)
    {

    }
}
