<?php

namespace Betta\Models\Observers;

use Betta\Models\SurveyQuestion;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyQuestionObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(SurveyQuestion $model)
    {
       $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(SurveyQuestion $model)
    {

    }
}
