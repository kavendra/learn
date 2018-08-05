<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Conference;

abstract class ConferenceFieldMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Conference
     */
    public $conference;

    /**
     * Inject instance
     *
     * @var Betta\Models\Profile
     */
    public $profile;

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
    protected $context = 'conference';

    /**
     * Create a new message instance.
     *
     * @see  Betta\Foundation\Mail\LogsCommunications
     * @return void
     */
    public function __construct(Conference $conference)
    {
        # Set the Speaker
        $this->conference = $conference;

        # Set the Main Profile
        $this->profile = $conference->createdBy;
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
