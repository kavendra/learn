<?php

namespace Betta\Models\Observers;

use Betta\Models\HcpProfile;
use Betta\Foundation\Eloquent\AbstractObserver;

class HcpProfileObserver extends AbstractObserver
{
    /**
     * Listen to the HcpProfile creating event.
     *
     * @param  HcpProfile  $model
     * @return void
     */
    public function creating(HcpProfile $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }


    /**
     * Listen to the HcpProfile created event.
     *
     * @param  HcpProfile  $model
     * @return void
     */
    public function created(HcpProfile $model)
    {
        $model->notes()->create(['content' => 'New HCP Profile Created']);
    }
}
