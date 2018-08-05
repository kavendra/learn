<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceCloseout;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceCloseoutObserver extends AbstractObserver
{
    

    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(ConferenceCloseout $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

       
	}

    

    /**
     * Listen to Conference update event
     *
     * @param  Conference $model
     * @return void
     */
    public function updated(ConferenceCloseout $model)
    {
        
       
        # Update the costs
        # Update the distance
    }


   
}
