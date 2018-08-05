<?php

namespace Betta\Models\Observers;

use Betta\Models\RequestMeta;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestMetaObserver extends AbstractObserver
{
    /**
     * Listen to the RequestMeta creating event.
     *
     * @param  RequestMeta  $model
     * @return void
     */
    public function creating(RequestMeta $model)
    {
        $this->setCreator($model);
    }
}
