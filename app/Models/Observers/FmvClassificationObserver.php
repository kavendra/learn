<?php

namespace Betta\Models\Observers;

use Betta\Models\FmvClassification;
use Betta\Foundation\Eloquent\AbstractObserver;

class FmvClassificationObserver extends AbstractObserver
{
    /**
     * Listen to the FmvClassification creating event.
     *
     * @param  FmvClassification  $model
     * @return void
     */
    public function creating(FmvClassification $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
