<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Betta\Models\Engagement;
use Betta\Foundation\Events\AbstractEngagementEvent as Event;

abstract class AbstractEngagementListener extends AbstractBettaListener
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $engagement;

    /**
     * Set the Nomination and
     *
     * @param  Event  $event
     * @return void
     */
    public function handle(Event $event)
    {
        # Set the Program for the reuse
        $this->setModel($event->engagement);
        # Dimsiss Alerts
        $this->dismiss($event);
        # Add Engagement accessor, just in case
        $this->setEngagement($event->engagement)->run();
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

    /**
     * Set the Engagement
     *
     * @param Betta\Models\Engagement $engagement
     * @return  Instance
     */
    protected function setEngagement(Engagement $engagement)
    {
        $this->contract = $contract;

        return $this;
    }

    /**
     * Access Engagement record
     *
     * @return Betta\Models\Engagement
     */
    protected function getEngagement()
    {
        return $this->engagement;
    }

    /**
     * Return Now expressed as Carbon
     *
     * @return Carbon\Carbon
     */
    protected function now()
    {
        return Carbon::now();
    }
}
