<?php

namespace Betta\Foundation\Listeners;

use Illuminate\Mail\Mailer;
use Betta\Models\ProgramLocation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProgramLocationEvent;

abstract class AbstractProgramLocationListener
{
    use InteractsWithQueue;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Mail\Mailer
     */
    protected $mail;


    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramLocation
     */
    protected $programLocation;


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
     * @param  AbstractProgramLocationEvent  $event
     * @return void
     */
    public function handle(AbstractProgramLocationEvent $event)
    {
        $this->setProgramLocation($event->programLocation)->run();
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
     * Set the ProgramLocation
     *
     * @param  ProgramLocation $programLocation
     * @return Instance
     */
    protected function setProgramLocation(ProgramLocation $programLocation)
    {
        $this->programLocation = $programLocation;

        return $this;
    }


    /**
     * Access ProgramLocation
     *
     * @return ProgramLocation
     */
    protected function getProgramLocation()
    {
        return $this->programLocation;
    }
}
