<?php

namespace Betta\Models\Observers;

use Betta\Models\SurveyDetail;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyDetailObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(SurveyDetail $model)
    {
       $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(SurveyDetail $model)
    {

    }
}
