<?php

namespace Betta\Foundation\Mail;

use Betta\Models\ProgramSpeaker;

abstract class SpeakerMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\ProgramSpeaker
     */
    public $speaker;

    /**
     * Resolve instance
     *
     * @var Betta\Models\Program
     */
    public $program;

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
    protected $context = 'speaker.program';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProgramSpeaker $programSpeaker)
    {
        $this->program = $programSpeaker->program;
        $this->speaker = $programSpeaker;
    }

    /**
     * Set the speaker
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return Instance
     */
    public function setSpeaker(ProgramSpeaker $programSpeaker)
    {
        $this->speaker = $programSpeaker;

        return $this;
    }

    /**
     * Get the speaker
     *
     * @return ProgramSpeaker $programSpeaker
     */
    public function getSpeaker()
    {
        return $this->speaker;
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
