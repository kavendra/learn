<?php

namespace Betta\Docusign\Exceptions;

use Betta\Docusign\Foundation\DocusignException;

class BadEnvelopeException extends DocusignException
{
    /**
     * @var string
     */
    protected $status = '500';


    /**
     * @var string
     */
    protected $title = 'Envelope if malformed';


    /**
     * @var string
     */
    protected $detail = "Bad Request: Envelope Id should be a 32 digit GUID in following format:  1a2b3c4d-1a2b-1a2b-1a2b-1a2b3c4d5e6f";


    /**
     * @return void
     */
    public function __construct()
    {
        # this till allow us to get unlimited arguments
        $message = $this->build( func_get_args() );

        # let the parent deal with Exception
        parent::__construct($message);
    }
}
