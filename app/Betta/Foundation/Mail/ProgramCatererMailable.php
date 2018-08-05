<?php

namespace Betta\Foundation\Mail;

use Betta\Models\ProgramCaterer;

abstract class ProgramCatererMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\ProgramCaterer
     */
    public $programCaterer;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'programCaterer';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ProgramCaterer $programCaterer)
    {
        $this->programCaterer = $programCaterer;
    }
}
