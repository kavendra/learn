<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Registration;
use Betta\Foundation\Mail\LogsCommunications;

abstract class ProgramRegistrationMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Pivots\ProgramFieldPivot
     */
    public $program;

    /**
     * Inject instance
     *
     * @var Betta\Models\Profile
     */
    public $profile;

    /**
     * Inject instance
     *
     * @var Betta\Models\Registration
     */
    public $registration;

     /**
     * Recipient profile or email
     *
     * @var string|Betta\Models\Profile
     */
    public $recipient;

    /**
     * In case we can idenitify what tempalte the view represents
     *
     * @var null|int
     */
    protected $template_id = null;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'program';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        # Set the Speaker
        $this->program = $registration->program;
        # Set the Registration
        $this->registration = $registration;
        # Set the Main Profile
        $this->profile = $registration->profile;
        # Set the Recipient
        $this->recipient = $registration->recipient;
    }

    /**
     * Build the email
     *
     * @return MailableContract
     */
    abstract public function build();

    /**
     * Compile Subject string
     *
     * @return stirng
     */
    abstract protected function getSubject();

    /**
     * Produce additional injectalbe data
     *
     * @return Array
     */
    protected function getData()
    {
        return [];
    }
}
