<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Request;
use Betta\Foundation\Events\AbstractRequestEvent as Event;

abstract class AbstractRequestListener extends AbstractBettaListener
{
    /**
     * Name the variable on the Listener instance
     *
     * @var string
     */
    protected $variable = 'request';
    /**
     * Set the Nomination and
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        $this->setModel($event->request, $this->variable)->run();
    }

    /**
     * Put all the necessary logic into the run section
     *
     * @return Void
     */
    abstract protected function run();

    /**
     * Notify the system
     *
     * @return Void
     */
    protected function notifySystem()
    {
        # We need to build a robust notifier
        $recipient = config('fls.system_email');
    }

    /**
     * Refresh the model in the listener
     *
     * @return void
     */
    protected function refresh()
    {
        # get the fresh instance of the model
        $fresh = $this->getModel()->fresh([]);
        # set the model anew
        $this->setModel($fresh, $this->variable);
    }
}
