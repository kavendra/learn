<?php

namespace Betta\Models\Observers;

use Betta\Models\CertificationType;
use Betta\Foundation\Eloquent\AbstractObserver;

class CertificationTypeObserver extends AbstractObserver
{
    /**
     * Listen to the CertificationType creating event.
     *
     * @param  CertificationType  $model
     * @return void
     */
    public function creating(CertificationType $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
