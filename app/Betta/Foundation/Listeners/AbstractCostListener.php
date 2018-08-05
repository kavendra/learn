<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Betta\Models\Cost;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractCostEvent;

abstract class AbstractCostListener
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Mail\Mailer
     */
    protected $mail;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mail = $mailer;
    }

    /**
     * Set the Nomination and
     *
     * @param  AbstractCostEvent  $event
     * @return void
     */
    public function handle(AbstractCostEvent $event)
    {
        $this->setCost($event->cost)->run();
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

        # We need to build a robust notifier
    }

    /**
     * Set the Cost
     *
     * @param   Cost $cost
     * @return  Instance
     */
    protected function setCost(Cost $cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Access Cost record
     *
     * @return Cost
     */
    protected function getCost()
    {
        return $this->cost;
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
