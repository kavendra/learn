<?php

namespace Betta\Services\Generator\Streams\Profile\Debarment\Exceptions;

use Exception;

class NoModelProvided extends Exception
{
    public $message = 'No Background Check record found';
}
