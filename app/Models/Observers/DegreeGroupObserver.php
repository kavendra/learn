<?php

namespace Betta\Models\Observers;

use Betta\Models\DegreeGroup;
use Betta\Foundation\Eloquent\AbstractObserver;

class DegreeGroupObserver extends AbstractObserver
{
    /**
     * Listen to the DegreeGroup creating event.
     *
     * @param  DegreeGroup  $model
     * @return void
     */
    public function creating(DegreeGroup $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
