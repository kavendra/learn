<?php

namespace Betta\Models\Observers;

use Betta\Models\TicketCategory;
use Betta\Foundation\Eloquent\AbstractObserver;

class TicketCategoryObserver extends AbstractObserver
{
    /**
     * Listen to the Ticket creating event.
     *
     * @param  Ticket  $model
     * @return void
     */
    public function creating(TicketCategory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Ticket Status ID
        //$model->setAttribute('ticket_status', $model->getAttribute('ticket_status') ?: $this->getInitialStatus() );
    }


    /**
     * Listen to the Program created event.
     *
     * @param  Program  $model
     * @return void
     */
    public function created(TicketCategory $model)
    {
        
    }


    /**
     * Listen to the Program saving event.
     *
     * @param  Program  $model
     * @return void
     */
    public function saving(TicketCategory $model)
    {
       
    }


    
}
