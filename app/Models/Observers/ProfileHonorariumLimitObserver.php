<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileHonorariumLimit;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileHonorariumLimitObserver extends AbstractObserver
{

    /**
     * Listen to the ProfileHonorariumLimit creating event.
     *
     * @param  ProfileHonorariumLimit  $model
     * @return void
     */
    public function creating(ProfileHonorariumLimit $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Valid From
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
    }

    /**
     * Listen to the ProfileHonorariumLimit created event.
     *
     * @param  ProfileHonorariumLimit  $model
     * @return void
     */
    public function created(ProfileHonorariumLimit $model)
    {

    }
}
