<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Betta\Models\MaxCap;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractMaxCapEvent;

abstract class AbstractMaxCapListener
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
     * @param  AbstractMaxCapEvent  $event
     * @return void
     */
    public function handle(AbstractMaxCapEvent $event)
    {
        $this->setMaxCap($event->maxCap)->run();
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
     * Set the MaxCap
     *
     * @param MaxCap $maxCap
     * @return  Instance
     */
    protected function setMaxCap(MaxCap $maxCap)
    {
        $this->maxCap = $maxCap;

        return $this;
    }

    /**
     * Access maxCap record
     *
     * @return MaxCap
     */
    protected function getMaxCap()
    {
        return $this->maxCap;
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
