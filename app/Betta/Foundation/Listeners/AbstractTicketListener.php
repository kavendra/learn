<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Ticket;
use Betta\Foundation\Events\AbstractTicketEvent as Event;

abstract class AbstractTicketListener extends AbstractBettaListener
{
    /**
     * Set the Ticket and run the events
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        # Set Ticket and Run
        $this->setModel($event->ticket, 'ticket')->run();
        # Dimsiss Alerts
        $this->dismiss($event);
    }

    /**
     * Put all the necessary logic into the run section
     *
     * @return Void
     */
    abstract protected function run();

    /**
     * Notify the system the Nomination has been approved
     *
     * @return Void
     */
    protected function notifySystem()
    {
        # We need to build a robust notifier
        $recipient = config('fls.system_email');
    }
}
