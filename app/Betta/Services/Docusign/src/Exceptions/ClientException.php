<?php

namespace Betta\Docusign\Exceptions;

use Betta\Docusign\Foundation\DocusignException;

class ClientException extends DocusignException
{
    /**
     * @var string
     */
    protected $status = '500';


    /**
     * @var string
     */
    protected $title = 'Client cannot be initiated';


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
