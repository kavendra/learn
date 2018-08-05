<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramToBudgetJar;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramToBudgetJarObserver extends AbstractObserver
{
    
   

    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(ProgramToBudgetJar $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
	}

   
}
