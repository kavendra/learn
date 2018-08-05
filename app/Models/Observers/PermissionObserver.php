<?php

namespace Betta\Models\Observers;

use Betta\Models\Permission;
use Betta\Foundation\Eloquent\AbstractObserver;

class PermissionObserver extends AbstractObserver
{
    /**
     * Listen to the Permission creating event.
     *
     * @param  Permission  $model
     * @return void
     */
    public function creating(Permission $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Permission created event.
     *
     * @param  Permission  $model
     * @return void
     */
    public function created(Permission $model)
    {

    }
}
