<?php

namespace Betta\Models\Observers;

use Betta\Models\DocusignRecipient;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocusignRecipientObserver extends AbstractObserver
{
    /**
     * Listen to the DocusignRecipient creating event.
     *
     * @param  DocusignRecipient  $model
     * @return void
     */
    public function creating(DocusignRecipient $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
