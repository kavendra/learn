<?php

namespace Betta\Models\Observers;

use Betta\Models\Department;
use Betta\Foundation\Eloquent\AbstractObserver;

class DepartmentObserver extends AbstractObserver
{
    /**
     * Listen to the News creating event.
     *
     * @param  News  $model
     * @return void
     */
    public function creating(News $model)
    {

    }
}
