<?php

namespace Betta\Models\Observers;

use Betta\Models\ContentLink;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContentLinkObserver extends AbstractObserver
{
    /**
     * Listen to the ContentLink creating event.
     *
     * @param  ContentLink  $model
     * @return void
     */
    public function creating(ContentLink $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }
}
