<?php

namespace Betta\Foundation\Listeners;

use Carbon\Carbon;
use Betta\Models\Document;
use Illuminate\Mail\Mailer;
use App\Http\Traits\FlashesMessages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractAttachedContextEvent;

abstract class AbstractAttachedContextListener
{
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
     * Set the DocumentContext and
     *
     * @param  AbstractAttachedContextEvent  $event
     * @return void
     */
    public function handle(AbstractAttachedContextEvent $event)
    {
        $this->setDocument($event->document);
        $this->setContext($event->context);
        $this->setReferenceName($event->reference_name)->run();
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
     * Set the Document
     *
     * @param   Document $document
     * @return  Instance
     */
    protected function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Access Document record
     *
     * @return Instance
     */
    protected function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the Context
     *
     * @param   Model $document
     * @return  Instance
     */
    protected function setContext(Model $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the context of document
     * This method can be overridden
     *
     * @param  Model  $model
     * @return Instance
     */
    protected function getContext()
    {
        return $this->context;
    }


    /**
     * Set the Reference Name
     *
     * @param   String
     * @return  Instance
     */
    protected function setReferenceName($reference_name)
    {
        $this->reference_name = $reference_name;

        return $this;
    }

    /**
     * Get the reference_name of document
     * This method can be overridden
     *
     * @param  Model  $model
     * @return String
     */
    protected function getReferenceName()
    {
        return $this->reference_name;
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
