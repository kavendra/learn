<?php

namespace Betta\Docusign\Exceptions;

use Betta\Docusign\Foundation\DocusignException;

class SignatureException extends DocusignException
{
    /**
     * @var string
     */
    protected $status = '500';


    /**
     * @return void
     */
    public function __construct()
    {
        # this will allow us to get unlimited arguments
        $message = $this->build( func_get_args() );

        # let the parent deal with Exception
        parent::__construct( $message, $this->getStatus() );
    }
}
