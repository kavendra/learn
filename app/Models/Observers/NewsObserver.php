<?php

namespace Betta\Models\Observers;

use Betta\Models\News;
use Betta\Foundation\Eloquent\AbstractObserver;

class NewsObserver extends AbstractObserver
{
    /**
     * Listen to the News creating event.
     *
     * @param  News  $model
     * @return void
     */
    public function creating(News $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Valid From if not provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }
}
