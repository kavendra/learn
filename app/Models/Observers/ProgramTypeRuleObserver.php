<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramTypeRule;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramTypeRuleObserver extends AbstractObserver
{
    /**
     * Listen to the Territory creating event.
     *
     * @param  Territory  $model
     * @return void
     */
    public function creating(ProgramTypeRule $model)
    {
        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }
}
