<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Pivots\ProgramFieldPivot;
use Betta\Foundation\Mail\LogsCommunications;

abstract class ProgramFieldMailable extends AbstractMailable
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
    public function __construct(ProgramFieldPivot $programFieldPivot)
    {
        # Set the Speaker
        $this->program = $programFieldPivot->program;
        # Set the Main Profile
        $this->profile = $programFieldPivot->profile;
        # Set the Recipient
        $this->recipient = $programFieldPivot->recipient;
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
