<?php

namespace Betta\Models\Traits;

trait NoTestTrait
{
    /**
     * Identify the DB field where the test flag is stored
     *
     * @var string
     */
    protected $testField = 'is_test';


    /**
     * Exclude the records marked as test
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeNoTest($query)
    {
        return $query->where( $this->getQualifiedTestField(), '!=', true);
    }


    /**
     * Qualify the test field aginst the table
     *
     * @return string
     */
    protected function getQualifiedTestField()
    {
        return $this->getTable().'.'.$this->testField;
    }
}
