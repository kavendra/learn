<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Illuminate\Mail\Mailer;
use Betta\Models\MaxCapIncrease;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractMaxCapIncreaseEvent;

abstract class AbstractMaxCapIncreaseListener
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
     * @param  AbstractMaxCapIncreaseEvent  $event
     * @return void
     */
    public function handle(AbstractMaxCapIncreaseEvent $event)
    {
        $this->setMaxCapIncrease($event->maxCapIncrease)->run();
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
     * Set the MaxCapIncrease
     *
     * @param   MaxCapIncrease $maxCapIncrease
     * @return  Instance
     */
    protected function setMaxCapIncrease(MaxCapIncrease $maxCapIncrease)
    {
        $this->maxCapIncrease = $maxCapIncrease;

        return $this;
    }

    /**
     * Access MaxCapIncrease record
     *
     * @return MaxCapIncrease
     */
    protected function getMaxCapIncrease()
    {
        return $this->maxCapIncrease;
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
