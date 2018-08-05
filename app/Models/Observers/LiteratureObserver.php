<?php

namespace Betta\Models\Observers;

use Betta\Models\Literature;
use Betta\Foundation\Eloquent\AbstractObserver;

class LiteratureObserver extends AbstractObserver
{
    /**
     * Listen to the Literature creating event.
     *
     * @param  Literature  $model
     * @return void
     */
    public function creating(Literature $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Literature created event.
     *
     * @param  Literature  $model
     * @return void
     */
    public function created(Literature $model)
    {
        # Void
    }
}
