<?php

namespace Betta\Models\Observers;

use Betta\Models\ContentCategory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContentCategoryObserver extends AbstractObserver
{
    /**
     * Listen to the ContentCategory creating event.
     *
     * @param  ContentCategory  $model
     * @return void
     */
    public function creating(ContentCategory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }
}
