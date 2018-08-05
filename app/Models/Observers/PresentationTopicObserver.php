<?php

namespace Betta\Models\Observers;

use Betta\Models\PresentationTopic;
use Betta\Foundation\Eloquent\AbstractObserver;

class PresentationTopicObserver extends AbstractObserver
{
    /**
     * Listen to the PresentationTopic creating event.
     *
     * @param  PresentationTopic  $model
     * @return void
     */
    public function creating(PresentationTopic $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the PresentationTopic created event.
     *
     * @param  PresentationTopic  $model
     * @return void
     */
    public function created(PresentationTopic $model)
    {

    }
}
