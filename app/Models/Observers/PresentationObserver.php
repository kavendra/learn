<?php

namespace Betta\Models\Observers;

use Betta\Models\Presentation;
use Betta\Foundation\Eloquent\AbstractObserver;

class PresentationObserver extends AbstractObserver
{
    /**
     * Listen to the Presentation creating event.
     *
     * @param  Presentation  $model
     * @return void
     */
    public function creating(Presentation $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current time if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the Presentation created event.
     *
     * @param  Presentation  $model
     * @return void
     */
    public function created(Presentation $model)
    {

    }
}
