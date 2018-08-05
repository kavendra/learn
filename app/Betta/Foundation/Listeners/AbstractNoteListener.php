<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Note;
use Betta\Foundation\Events\AbstractNoteEvent as Event;

abstract class AbstractNoteListener extends AbstractBettaListener
{
    /**
     * Set the Nomination and
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        # Set Note and Run
        $this->setModel($event->note, 'note')->run();
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
