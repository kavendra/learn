<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramPresentationTopic;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramPresentationTopicObserver extends AbstractObserver
{

    /**
     * Listen to the ProgramPresentationTopic creating event.
     *
     * @param  ProgramPresentationTopic  $model
     * @return void
     */
    public function creating(ProgramPresentationTopic $model)
    {

    }
}
