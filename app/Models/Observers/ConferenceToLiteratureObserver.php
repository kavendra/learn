<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceToLiterature;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceToLiteratureObserver extends AbstractObserver
{
    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(ConferenceToLiterature $model)
    {
        # Set Current User as Creator
        $model->setAttribute('material_status', 1);

       
    }

    /**
     * Listen to the  created event.
     *
     * @param  AvItem  $model
     * @return void
     */
    public function created(ProfileToBrand $model)
    {

    }

   
}
