<?php

namespace Betta\Foundation\Listeners;

use Illuminate\Mail\Mailer;
use Betta\Models\ProgramSpeaker;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProgramSpeakerEvent;

abstract class AbstractProgramSpeakerListener
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Mail\Mailer
     */
    protected $mail;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

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
     * @param  AbstractProgramSpeakerEvent  $event
     * @return void
     */
    public function handle(AbstractProgramSpeakerEvent $event)
    {
        $this->setProgramSpeaker($event->programSpeaker)->run();
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
     * Set the ProgramSpeaker
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return Instance
     */
    protected function setProgramSpeaker(ProgramSpeaker $programSpeaker)
    {
        $this->programSpeaker = $programSpeaker;

        return $this;
    }

    /**
     * Access ProgramSpeaker
     *
     * @return ProgramSpeaker
     */
    protected function getProgramSpeaker()
    {
        return $this->programSpeaker;
    }

    /**
     * Resolve Program from Speaker
     *
     * @return Betta\Models\Program
     */
    protected function program()
    {
        return $this->getProgramSpeaker()->program;
    }
}
