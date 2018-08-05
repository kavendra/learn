<?php

namespace Betta\Models\Observers;

use Betta\Models\NewsPermission;
use Betta\Foundation\Eloquent\AbstractObserver;

class NewsPermissionObserver extends AbstractObserver
{
    /**
     * Listen to the NewsPermission creating event.
     *
     * @param  NewsPermission  $model
     * @return void
     */
    public function creating(NewsPermission $model)
    {
        # Void for the time being
    }
}
