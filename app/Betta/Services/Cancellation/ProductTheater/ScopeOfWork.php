<?php

namespace Betta\Services\Cancellation\ProductTheater;

trait ScopeOfWork
{
    /**
     * True if count of the Confirmed by FIELD is non empty
     *
     * @return boolean
     */
    protected function limitedScope()
    {
        return ! $this->fullScope();
    }

    /**
     * Opposite of limitedScope()
     *
     * @return boolean
     */
    protected function fullScope()
    {
        return $this->program->programCaterers->isNotEmpty();
    }
}
