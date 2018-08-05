<?php

namespace Betta\Models\Observers;

use Betta\Models\ContentMaterial;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContentMaterialObserver extends AbstractObserver
{
    /**
     * Listen to the ContentMaterial creating event.
     *
     * @param  ContentMaterial  $model
     * @return void
     */
    public function creating(ContentMaterial $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }
}
