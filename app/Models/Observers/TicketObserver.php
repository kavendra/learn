<?php

namespace Betta\Models\Observers;

use Betta\Models\Ticket;
use Betta\Models\TicketStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class TicketObserver extends AbstractObserver
{
    /**
     * Create the list of Ticket events
     *
     * @var Array
     */
    protected $statusEvents = [
        TicketStatus::OPEN      => 'App\Events\Ticket\Open',
        TicketStatus::SUBMITTED => 'App\Events\Ticket\Submitted',
        TicketStatus::CLOSED    => 'App\Events\Ticket\Closed',
        TicketStatus::PENDING   => 'App\Events\Ticket\Pending',
        TicketStatus::ON_HOLD   => 'App\Events\Ticket\OnHold',
    ];

    /**
     * Default status
     *
     * @var int
     */
    protected $default = TicketStatus::SUBMITTED;

    /**
     * Listen to the Ticket creating event.
     *
     * @param  Ticket  $model
     * @return void
     */
    public function creating(Ticket $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Ticket Status ID
        $model->setAttribute('ticket_status_id', $model->getAttribute('ticket_status_id') ?: $this->default );
    }

    /**
     * Listen to the Ticket saved event.
     *
     * @param  Ticket $ticket
     * @return void
     */
    public function saved(Ticket $model)
    {
       if($model->isDirty($model->getStatusFieldName())){
            $this->fireStatusEvents($model);
       }
    }

    /**
     * Complete the Status Change and fire event if present
     *
     * @param  Ticket $model
     * @return void
     */
    protected function fireStatusEvents(Ticket $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($model->getStatusFieldName());

        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());

        # Add history
        $model->histories()->create(compact('from_status_id','to_status_id'));

        # Map the status to events and fire them all
        if ($event = array_get($this->getStatusEvents($model), $to_status_id)){
            event (new $event($model));
        }
    }

    /**
     * Return status events array
     *
     * @param  Ticket $model
     * @return Array
     */
    protected function getStatusEvents(Ticket $model)
    {
        return $this->statusEvents;
    }
}
