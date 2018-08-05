<?php

namespace Betta\Models\Observers;

use Artisan;
use Betta\Models\User;
use Betta\Foundation\Eloquent\AbstractObserver;

class UserObserver extends AbstractObserver
{
    /**
     * Listen to the User creating event.
     *
     * @param  User  $model
     * @return void
     */
    public function creating(User $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the user created event
     *
     * @param  User   $model
     * @return void
     */
    public function created(User $model)
    {
        $this->clearCache();
    }

    /**
     * Listen to User saving event
     *
     * @param  User   $model
     * @return Void
     */
	public function saving(User $model)
    {
        $model->setAttribute('email', $model->getAttribute('username') ?: '' );
    }

    /**
     * Clear the cache for the users
     *
     * @return Void
     */
    protected function clearCache()
    {
        Artisan::call('cache:clear');
    }
}
