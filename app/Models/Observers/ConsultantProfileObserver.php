<?php

namespace Betta\Models\Observers;

use Betta\Models\ConsultantProfile;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConsultantProfileObserver extends AbstractObserver
{
    /**
     * List the caches that need to be flushed
     *
     * @var array
     */
    protected $flushable = [
        'consultants',
    ];

    /**
     * Listen to the ConsultantProfile creating event.
     *
     * @param  ConsultantProfile  $model
     * @return void
     */
    public function creating(ConsultantProfile $model)
    {
        $this->setCreator($model);
    }

    /**
     * Listen to the ConsultantProfile deleted event.
     *
     * @param  ConsultantProfile  $model
     * @return void
     */
    public function deleted(ConsultantProfile $model)
    {
        if($profile = $model->profile){
            $profile->addPrivateNote('[AUTO] Consultant Profile removed');
        }
    }
}
