<?php

namespace Betta\Models\Observers;

use Betta\Models\DocusignRequest;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocusignRequestObserver extends AbstractObserver
{
    /**
     * Listen to the DocusignRequest creating event.
     *
     * @param  DocusignRequest  $model
     * @return void
     */
    public function creating(DocusignRequest $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
