<?php

namespace Betta\Models\Observers;

use Betta\Models\LoginHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class LoginHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the LoginHistory creating event.
     *
     * @param  LoginHistory  $model
     * @return void
     */
    public function creating(LoginHistory $model)
    {
        # Set IP Address, if not provided
        $model->setAttribute('ip_address', $model->getAttribute('ip_address') ?: $this->getIpAddress() );

        # Set Simulating User Id
        $model->setAttribute('simulant_id', $model->getAttribute('simulant_id') ?: $this->getSimulatingUserId() );
    }

    /**
     * Listen to the LoginHistory created event.
     *
     * @param  LoginHistory  $model
     * @return void
     */
    public function created(LoginHistory $model)
    {

    }


    /**
     * Resolve IP address from the request
     *
     * @return string
     */
    protected function getIpAddress()
    {
        return request()->ip();
    }
}
