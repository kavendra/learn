<?php

namespace Betta\Models\Observers;

use Betta\Models\SurveyAnswer;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyAnswerObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(SurveyAnswer $model)
    {
       $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(SurveyAnswer $model)
    {

    }
}
