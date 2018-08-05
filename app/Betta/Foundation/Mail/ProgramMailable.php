<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Program;

abstract class ProgramMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Program
     */
    public $program;

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
    public function __construct(Program $program)
    {
        $this->program = $program;
    }
}
