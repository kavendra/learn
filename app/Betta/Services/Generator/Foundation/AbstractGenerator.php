<?php

namespace Betta\Services\Generator\Foundation;

use Illuminate\Support\MessageBag;

abstract class AbstractGenerator
{
    /**
     * Share Errors
     *
     * @var MessageBag
     */
    protected $errors;


    /**
     * Get errors or create new Bag
     *
     * @return MessageBag
     */
    protected function getErrors()
    {
        return $this->errors ?: $this->errors = app()->make(MessageBag::class);
    }
}
