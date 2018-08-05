<?php

namespace Betta\Models\Observers;

use Betta\Models\Document;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocumentObserver extends AbstractObserver
{
    /**
     * Listen to the Document creating event.
     *
     * @param  Document  $model
     * @return void
     */
    public function creating(Document $model)
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
    public function created(Document $model)
    {
        # Crawl the document and read its contents into meta
    }
}
