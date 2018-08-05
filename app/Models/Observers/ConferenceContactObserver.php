<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceContact;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceContactObserver extends AbstractObserver
{
    /**
     * Listen to the Document creating event.
     *
     * @param  Document  $model
     * @return void
     */
    public function creating(ConferenceContact $model)
    {
        # Set Current Document as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Document created event.
     *
     * @param  Document  $model
     * @return void
     */
    public function created(ConferenceContact $model)
    {
        # Crawl the document and read its contents into meta
    }
}
