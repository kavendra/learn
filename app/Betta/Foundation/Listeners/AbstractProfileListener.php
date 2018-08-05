<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Profile;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProfileEvent;

abstract class AbstractProfileListener
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
     * @param  AbstractProfileEvent  $event
     * @return void
     */
    public function handle(AbstractProfileEvent $event)
    {
        $this->setProfile($event->profile)->run();
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
     * Set the Profile
     *
     * @param Profile $profile
     * @return  Instance
     */
    protected function setProfile(Profile $profile)
    {
        $this->profile = $profile;

        return $this;
    }


    /**
     * Access Profile record
     *
     * @return Profile
     */
    protected function getProfile()
    {
        return $this->profile;
    }
}
