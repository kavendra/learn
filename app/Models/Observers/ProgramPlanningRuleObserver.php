<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramPlanningRule;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramPlanningRuleObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramPlanningRule creating event.
     *
     * @param  ProgramPlanningRule  $model
     * @return void
     */
    public function creating(ProgramPlanningRule $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProgramPlanningRule created event.
     *
     * @param  ProgramPlanningRule  $model
     * @return void
     */
    public function created(ProgramPlanningRule $model)
    {

    }
}
