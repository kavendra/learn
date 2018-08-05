<?php

namespace Betta\Foundation\Listeners;

use Illuminate\Mail\Mailer;
use Betta\Models\ProfilePaymentMethod;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProfilePaymentMethodEvent;

abstract class AbstractProfilePaymentMethodListener
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
     * @param  AbstractProfilePaymentMethodEvent  $event
     * @return void
     */
    public function handle(AbstractProfilePaymentMethodEvent $event)
    {
        $this->setPaymentMethod($event->paymentMethod)->run();
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
     * Set the ProfilePaymentMethod
     *
     * @param ProfilePaymentMethod $paymentMethod
     * @return  Instance
     */
    protected function setPaymentMethod(ProfilePaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Access ProfilePaymentMethod record
     *
     * @return ProfilePaymentMethod
     */
    protected function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
}
