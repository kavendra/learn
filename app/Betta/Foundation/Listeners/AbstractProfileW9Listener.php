<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\ProfileW9;
use Betta\Foundation\Events\AbstractProfileW9Event as Event;

abstract class AbstractProfileW9Listener extends AbstractBettaListener
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProfileW9
     */
    protected $w9;

    /**
     * Set the Nomination and
     *
     * @param  AbstractProfileW9Event $event
     * @return void
     */
    public function handle(Event $event)
    {
        # Set the Program for the reuse
        $this->setModel($event->w9)->run();
        # Dimsiss Alerts
        $this->dismiss($event);
        # Create new alerts
        $this->alert();
    }

    /**
     * Put all the necessary logic into the run section
     *
     * @return Void
     */
    abstract protected function run();
}
