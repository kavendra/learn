<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Request;

abstract class RequestMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Request
     */
    public $request;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'request';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
