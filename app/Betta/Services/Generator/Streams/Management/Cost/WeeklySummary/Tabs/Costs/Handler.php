<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\Costs;

use Betta\Models\Cost;
use Betta\Services\Generator\Foundation\AbstratRowHandler as RowHandler;

class Handler extends RowHandler
{
    /**
     * Columns
     *
     * @var Array
     */
    protected $keys = [
        'ID',
        'Context Type',
        'Context ID',
        'Payee',
        'Cost Item',
        'Estimated',
        'Actual',
    ];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Cost
     */
    protected $cost;

    /**
     * Create new class
     *
     * @param Cost $cost
     */
    public function __construct(Cost $cost)
    {
        $this->cost = $cost;
    }

    /**
     * Get Id
     *
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->cost->id;
    }

    /**
     * Get Context Type
     *
     * @return string
     */
    public function getContextTypeAttribute()
    {
        return data_get($this->cost,'context.label');
    }

    /**
     * Get Context ID
     *
     * @return string
     */
    public function getContextIdAttribute()
    {
        return $this->cost->context_id;
    }

    /**
     * Get Payee, if present
     *
     * @return string
     */
    public function getPayeeAttribute()
    {
        return data_get($this->cost,'payee.preferred_name');
    }

    /**
     * Get Cost Item label
     *
     * @return string
     */
    public function getCostItemAttribute()
    {
        return $this->cost->label;
    }

    /**
     * Get estimated cost
     *
     * @return string
     */
    public function getEstimatedAttribute()
    {
        return (float)$this->cost->allocated;
    }

    /**
     * Get real cost
     *
     * @return string
     */
    public function getActualAttribute()
    {
        return (float)$this->cost->real;
    }
}
