<?php

namespace Betta\Models\Observers;

use Betta\Models\DocumentContext;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocumentContextObserver extends AbstractObserver
{
    /**
     * Listen to the DocumentContext creating event.
     *
     * @param  DocumentContext  $model
     * @return void
     */
    public function creating(DocumentContext $model)
    {
        # Void
    }

    /**
     * Listen to the DocumentContext created event.
     *
     * @param  DocumentContext  $model
     * @return void
     */
    public function created(DocumentContext $model)
    {
        # Void
    }
}
