<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceToBudgetJar;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceToBudgetJarObserver extends AbstractObserver
{
    
   

    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(ConferenceToBudgetJar $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
	}

   
}
