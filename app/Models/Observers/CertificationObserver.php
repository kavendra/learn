<?php

namespace Betta\Models\Observers;

use Betta\Models\Certification;
use Betta\Foundation\Eloquent\AbstractObserver;

class CertificationObserver extends AbstractObserver
{
    /**
     * Listen to the Certification creating event.
     *
     * @param  Certification  $model
     * @return void
     */
    public function creating(Certification $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId());
        # Set Current User as Certifier
        $model->setAttribute('certifier_id', $model->getAttribute('certifier_id') ?: $this->getUserId());
    }

    /**
     * Listen to the Certification deleting event.
     *
     * @param  Certification  $model
     * @return void
     */
    public function deleting(Certification $model)
    {
        # Set Current User as Certifier
        $model->setAttribute('deleted_by_id', $model->getAttribute('deleted_by_id') ?: $this->getUserId());
    }
}
