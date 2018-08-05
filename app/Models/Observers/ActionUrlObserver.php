<?php

namespace Betta\Models\Observers;

use Betta\Models\ActionUrl;
use Betta\Foundation\Eloquent\AbstractObserver;

class ActionUrlObserver extends AbstractObserver
{
    /**
     * Listen to the ActionUrl creating event.
     *
     * @param  ActionUrl  $model
     * @return void
     */
    public function creating(ActionUrl $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current User as Creator
        $model->setAttribute('md5_url', $model->getAttribute('md5_url') ?: $this->makeRandomMd5() );
    }


    /**
     * Make totally random MD5 string
     *
     * @return string
     */
    protected function makeRandomMd5()
    {
        return md5(bcrypt(microtime()));
    }
}
