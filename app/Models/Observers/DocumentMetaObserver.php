<?php

namespace Betta\Models\Observers;

use Betta\Models\DocumentMeta;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocumentMetaObserver extends AbstractObserver
{
    /**
     * Listen to the DocumentMeta creating event.
     *
     * @param  DocumentMeta  $model
     * @return void
     */
    public function creating(DocumentMeta $model)
    {
        # Set Current DocumentMeta as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the DocumentMeta created event.
     *
     * @param  DocumentMeta  $model
     * @return void
     */
    public function created(DocumentMeta $model)
    {

    }
}
