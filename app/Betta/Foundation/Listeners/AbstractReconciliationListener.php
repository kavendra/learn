<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Reconciliation;
use Betta\Foundation\Events\AbstractReconciliationEvent as Event;

abstract class AbstractReconciliationListener extends AbstractBettaListener
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $reconciliation;

    /**
     * Set the Nomination and
     *
     * @param  AbstractReconciliationEvent  $event
     * @return void
     */
    public function handle(Event $event)
    {
        # Set the Program for the reuse
        $this->setModel($event->reconciliation);
        # Dimsiss Alerts
        $this->dismiss($event);
        # Set Reconciliation
        $this->setReconciliation($event->reconciliation)->run();
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
        $recipient = config('fls.system_email');
    }

    /**
     * Set the Reconciliation record
     *
     * @param Reconciliation $reconciliation
     * @return  Betta\Models\Reconciliation
     */
    protected function setReconciliation(Reconciliation $reconciliation)
    {
        $this->reconciliation = $reconciliation;

        return $this;
    }

    /**
     * Access Reconciliation record
     *
     * @return Betta\Models\Reconciliation
     */
    protected function getReconciliation()
    {
        return $this->reconciliation;
    }
}
