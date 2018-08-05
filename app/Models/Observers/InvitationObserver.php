<?php

namespace Betta\Models\Observers;

use Betta\Models\Invitation;
use Betta\Foundation\Eloquent\AbstractObserver;

class InvitationObserver extends AbstractObserver
{
    /**
     * Listen to the Invitation creating event.
     *
     * @param  Invitation  $model
     * @return void
     */
    public function creating(Invitation $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Active by default
        $model->setAttribute('is_active', $model->getAttribute('is_active') ?: true );
    }


    /**
     * Listen to the Invitation saving event.
     *
     * @param  Invitation  $model
     * @return void
     */
    public function saving(Invitation  $model)
    {
        $model->setAttribute('weight', $model->getAttribute('weight') ?: 0 );
    }
}
