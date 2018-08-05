<?php

namespace Betta\Services\Canvass;

use DbView;
use Betta\Models\Profile;
use Betta\Models\ProgramCaterer;
use Illuminate\Support\Collection;
use Betta\Models\CommunicationTemplate;

class Canvass
{
    /**
     * Bind the application
     *
     * @var
     */
    protected $app;

    /**
     * Bind the Instance
     *
     * @var Betta\Models\CommunicationTemplate
     */
    protected $template;

    /**
     * Bind the Instance
     *
     * @var Flynsarmy\DbBladeCompiler\DbView
     */
    protected $compiler;

    /**
     * Injectable data to merge into Template
     *
     * @var array
     */
    protected $data = [];

    /**
     * Flag to pass to generators to see if the files need to be generated in fact
     *
     * @var boolean
     */
    protected $preview = true;

    /**
     * Subject of the email
     *
     * @var string
     */
    protected $subject;

    /**
     * Body of the email
     *
     * @var string
     */
    protected $body;

    /**
     * Recipients of the email
     *
     * @var Collection?
     */
    protected $recipients;

    /**
     * Recipient of the email
     *
     * @var Collection?
     */
    protected $to;

    /**
     * Carbon Copy Recipient of the email
     *
     * @var Array
     */
    protected $cc = [];

    /**
     * Black Carbon Copy Recipient of the email
     *
     * @var Array
     */
    protected $bcc = [];

    /**
     * Sender of the email
     *
     * @var array
     */
    protected $from;


    /**
     * Sender Context
     *
     * @var Model
     */
    protected $context;

    /**
     * Collected list of attachments
     *
     * @var Collection
     */
    protected $attachments = [];

    /**
     * Create new instance of the Tempalte
     *
     * @param CommunicationTemplate $template
     */
    public function __construct(CommunicationTemplate $template, $data = [])
    {
        $this->setTemplate($template)->setData($data);

        # make compiler
        $this->setCompiler();
    }


    /**
     * Magic accessor of functions as values
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        # compile method
        $method = 'get'.ucfirst($name);

        # If the method exists, return it
        if (method_exists($this, $method)){
            return $this->$method();
        }

        return $this->$name;
    }


    /**
     * Resolve Subject from Template
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->render('subject');
    }


    /**
     * Resolve Body from Template
     *
     * @return string
     */
    public function getBody()
    {
        return $this->render('html');
    }


    /**
     * Resole Label from the Template
     *
     * @return string
     */
    public function getLabel()
    {
        return object_get($this->template, 'label');
    }


    /**
     * Collect Attachments for the Template
     *
     * @return Collection?
     */
    public function getAttachments()
    {
        # Attachments tht have been added to Canvass
        # Those are arbitrary
        $attachments = collect($this->attachments)->keyBy('uri');
        # the attachments come from two spots:
        # 1. The attachments to the Comm Template
        # 2. The attachments that are generated
        # Format of attachments should be specific:
        # Path of the file, optionss (like file name);
        return $attachments->merge($this->getBaseAttachments()->keyBy('uri'));
    }


    /**
     * Resolve the Base Attachments for the communication
     *
     * @return Collection
     */
    protected function getBaseAttachments()
    {
        return object_get($this->getTemplate(), 'documents', collect([]));
    }


    /**
     * Get Receipients or create a new collection of them
     *
     * @return Collection
     */
    public function getRecipients()
    {
        return $this->recipients ?: $this->recipients = collect([]);
    }


    /**
     * Add a number of attachment
     *
     * @param  mixed $argument
     * @param  array  $options
     * @return Instance
     */
    public function attach($argument, $options = [])
    {
        if($argument instanceof Collection){
            $argument->each(function($document){
                $this->addAttachment($document->uri, $document->attachable_options );
            });
        } else {
            $this->addAttachment($argument, $options );
        }

        return $this;
    }


    /**
     * Push an element into Attachments
     *
     * @param array $array
     */
    public function addAttachment($uri, $options = [])
    {
        $this->attachments[] = compact('uri', 'options');
    }


    /**
     * Set Recipients
     *
     * @param mixed $recipient
     */
    public function addRecipient($recipient)
    {
        # If we have a collection, let's iterate and run the same function for the member
        if ($recipient instanceof Collection){
            $recipient->each(function($member){
                $this->addRecipient($member);
            });
        } else {
            $this->getRecipients()->push($recipient);
        }

        return $this;
    }


    /**
     * Add the email to CC array
     * Only email is accepted
     *
     * @param  email  $email
     * @return void
     */
    public function cc($email)
    {
        $this->cc[] = $email;
    }

    /**
     * Add the email to BCC array
     * Only email is accepted
     *
     * @param  email  $email
     * @return void
     */
    public function bcc($email)
    {
        $this->bcc[] = $email;
    }


    /**
     * Return Recipients Carbon Copy
     *
     * @return Collection?
     */
    public function getCc()
    {
        $recipients = array_merge($this->cc, object_get( $this->template, 'cc', []));

        return array_filter($recipients);
    }

    /**
     * Return Recipients Black Carbon Copy
     *
     * @return Collection?
     */
    public function getBcc()
    {
        $recipients = array_merge($this->bcc, object_get( $this->template, 'bcc', []));

        return array_filter($recipients);
    }


    /**
     * Set the template
     *
     * @param CommunicationTemplate $template
     * @return Instance
     */
    public function setTemplate(CommunicationTemplate $template)
    {
        # set the template
        $this->template = $template;

        # set template view
        $this->view = object_get($template, 'view_template');

        return $this;
    }


    /**
     * Get the template
     *
     * @param CommunicationTemplate $template
     * @return Instance
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Resolve the view from the
     *
     * @return string
     */
    public function getView()
    {
        return object_get($this->template, 'view_template');
    }


    /**
     * Set the template
     *
     * @param array $data
     * @return Instance
     */
    public function setData($data = array())
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Set the Canvass Context
     *
     * @param Model $context
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }


    /**
     * Get the Canvass Context
     *
     * @return Model | null
     */
    public function getContext()
    {
        return $this->context;
    }


    /**
     * Render a field
     *
     * @param  string $field
     * @return string
     */
    protected function render($field)
    {
        return $this->getCompiler()->field($field)->with( $this->data )->render();
    }


    /**
     * Make Compiler
     *
     * @return DbView
     */
    protected function getCompiler()
    {
        return $this->compiler ?: $this->setCompiler();
    }


    /**
     * Make Compiler
     *
     * @return DbView
     */
    protected function setCompiler()
    {
        return $this->compiler = DbView::make( $this->getTemplate() );
    }
}
