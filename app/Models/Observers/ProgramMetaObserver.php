<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramMeta;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramMetaObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramMeta creating event.
     *
     * @param  ProgramMeta  $model
     * @return void
     */
    public function creating(ProgramMeta $model)
    {
        # Set Current ProgramMeta as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }
}
