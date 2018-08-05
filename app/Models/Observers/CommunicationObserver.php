<?php

namespace Betta\Models\Observers;

use Betta\Models\Communication;
use Betta\Foundation\Eloquent\AbstractObserver;

class CommunicationObserver extends AbstractObserver
{
    /**
     * Listen to the Communication creating event.
     *
     * @param  Communication  $model
     * @return void
     */
    public function creating(Communication $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
