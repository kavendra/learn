<?php

namespace Betta\Models\Observers;

use Betta\Models\DocusignEnvelope;
use Betta\Foundation\Eloquent\AbstractObserver;

class DocusignEnvelopeObserver extends AbstractObserver
{
    /**
     * Listen to the DocusignEnvelope creating event.
     *
     * @param  DocusignEnvelope  $model
     * @return void
     */
    public function creating(DocusignEnvelope $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
