<?php

namespace Betta\Foundation\Listeners;

use Betta\Models\Training;
use Illuminate\Mail\Mailer;
use App\Http\Traits\FlashesMessages;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractTrainingEvent;

abstract class AbstractTrainingListener implements ShouldQueue
{
    /**
     * Listner can flash messages to session
     */
    use FlashesMessages;

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
     * @param  AbstractTrainingEvent  $event
     * @return void
     */
    public function handle(AbstractTrainingEvent $event)
    {
        $this->setTraining( $event->training )->run();
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
     * Set the Training
     *
     * @param   Training $training
     * @return  Instance
     */
    protected function setTraining(Training $training)
    {
        $this->training = $training;

        return $this;
    }

    /**
     * Access Training record
     *
     * @return Training
     */
    protected function getTraining()
    {
        return $this->training;
    }
}
