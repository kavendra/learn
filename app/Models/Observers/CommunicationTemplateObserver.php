<?php

namespace Betta\Models\Observers;

use Betta\Models\CommunicationTemplate;
use Betta\Foundation\Eloquent\AbstractObserver;

class CommunicationTemplateObserver extends AbstractObserver
{
    /**
     * Listen to the CommunicationTemplate creating event.
     *
     * @param  CommunicationTemplate  $model
     * @return void
     */
    public function creating(CommunicationTemplate $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
