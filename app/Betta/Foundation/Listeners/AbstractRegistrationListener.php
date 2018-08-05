<?php

namespace Betta\Foundation\Listeners;

use Illuminate\Mail\Mailer;
use Betta\Models\Registration;
use App\Http\Traits\FlashesMessages;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractRegistrationEvent;

abstract class AbstractRegistrationListener implements ShouldQueue
{
    use FlashesMessages;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Mail\Mailer
     */
    protected $mail;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Registration
     */
    protected $registration;

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
     * @param  AbstractRegistrationEvent  $event
     * @return void
     */
    public function handle(AbstractRegistrationEvent $event)
    {
        # Set the Registration for the reuse
        $this->setRegistration($event->registration)->run();
        # Dimsiss Alerts
        $this->dismiss($event);
        # Create new alerts
        $this->alert($event);
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
     * Dismiss all the items that needs to be dismissed
     *
     * @return Void
     */
    protected function dismiss(AbstractRegistrationEvent $event)
    {
        # $this->registration->alerts()->byEvent(get_class($event))->undismissed()
        #     ->dismiss(['dismissed_by'=>$this->getProfileId()]);
    }

    /**
     * Alert all receipients that need to be notified
     *
     * @return Void
     */
    protected function alert(AbstractRegistrationEvent $event)
    {

    }

    /**
     * Set the Registration
     *
     * @param  Registration $registration
     * @return $this
     */
    protected function setRegistration(Registration $registration)
    {
        $this->registration = $registration;

        return $this;
    }

    /**
     * Access Registration $registration
     *
     * @return Registration
     */
    protected function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Return CLU
     *
     * @return int
     */
    protected function getProfileId()
    {
        return data_get($this->getProfile(),'profile_id', config('betta.default_profile_id'));
    }

    /**
     * Resolve the User from the Currently Logged in State
     *
     * @return User | null
     */
    protected function getProfile()
    {
        return auth()->user();
    }
}
