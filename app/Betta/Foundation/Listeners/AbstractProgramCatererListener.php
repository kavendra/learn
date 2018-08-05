<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\ProgramCaterer;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractProgramCatererEvent;

abstract class AbstractProgramCatererListener
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
     * @var Betta\Models\ProgramCaterer
     */
    protected $programCaterer;


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
     * @param  AbstractProgramCatererEvent  $event
     * @return void
     */
    public function handle(AbstractProgramCatererEvent $event)
    {
        $this->setProgramCaterer($event->programCaterer)->run();
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
     * Set the ProgramCaterer
     *
     * @param  ProgramLocation $programCaterer
     * @return Instance
     */
    protected function setProgramCaterer(ProgramCaterer $programCaterer)
    {
        $this->programCaterer = $programCaterer;

        return $this;
    }


    /**
     * Access ProgramCaterer
     *
     * @return ProgramCaterer
     */
    protected function getProgramCaterer()
    {
        return $this->programCaterer;
    }


    /**
     * Get the recipients of the email from the models context
     * This method can be overridden
     *
     * @param  Model  $model
     * @return mixed
     */
    protected function getManager()
    {
        return data_get($this->programCaterer->program->primary_pm,'id');
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
     * Resolve the User from the Auth Guard
     *
     * @return User | null
     */
    protected function getProfile()
    {
        return auth()->user();
    }

}
