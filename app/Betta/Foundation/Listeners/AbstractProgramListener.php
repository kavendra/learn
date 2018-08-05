<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Program;
use Illuminate\Mail\Mailer;
use App\Http\Traits\FlashesMessages;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProgramEvent;

abstract class AbstractProgramListener implements ShouldQueue
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
     * @var Betta\Models\Program
     */
    protected $program;

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
     * @param  AbstractProgramEvent  $event
     * @return void
     */
    public function handle(AbstractProgramEvent $event)
    {
        # Set the Program for the reuse
        $this->setProgram($event->program)->run();
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
    protected function dismiss(AbstractProgramEvent $event)
    {
        $this->program->alerts()->byEvent(get_class($event))->undismissed()
             ->dismiss(['dismissed_by'=>$this->getProfileId()]);
    }

    /**
     * Alert all receipients that need to be notified
     *
     * @return Void
     */
    protected function alert(AbstractProgramEvent $event)
    {

    }

    /**
     * Set the Program
     *
     * @param  Program $program
     * @return $this
     */
    protected function setProgram(Program $program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Access Program $program
     *
     * @return Program
     */
    protected function getProgram()
    {
        return $this->program;
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
