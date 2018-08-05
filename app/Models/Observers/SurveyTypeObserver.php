<?php

namespace Betta\Models\Observers;

use Betta\Models\SurveyType;
use Betta\Foundation\Eloquent\AbstractObserver;

class SurveyTypeObserver extends AbstractObserver
{

    /**
     * Listen to the Alert creating event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function creating(SurveyType $model)
    {
       
    }

    /**
     * Listen to the Alert created event.
     *
     * @param  Alert  $model
     * @return void
     */
    public function created(SurveyType $model)
    {

    }
}
