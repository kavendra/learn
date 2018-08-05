<?php

namespace Betta\Services\Generator\Streams\Foundation;

use Betta\Services\Generator\Foundation\AbstractGenerator as FoundationAbstractGenerator;

abstract class AbstractGenerator extends FoundationAbstractGenerator
{
    /**
     * Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Set Arguments
     *
     * @param   array $arguments
     * @return  $this
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Return arguments
     *
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Resolve a single argument
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function argument($key, $default = null)
    {
        return data_get($this->arguments, $key, $default);
    }
}
