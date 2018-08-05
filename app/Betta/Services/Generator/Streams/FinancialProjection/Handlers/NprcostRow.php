<?php

namespace Betta\Services\Generator\Streams\FinancialProjection\Handlers;

use Betta\Models\NprCost;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class NprcostRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $nprcost;


    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Type',
        'Invoice Date',
        'Brand',
        'Cost',
    ];


    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(NprCost $nprcost)
    {
        $this->nprcost = $nprcost;
    }

    /**
     * Get Cost item type
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return data_get($this->nprcost, 'costItem.label');
    }

    /**
     * Get Invoice Date
     *
     * @return string
     */
    public function getInvoiceDateAttribute()
    {
        return  excel_date($this->nprcost->invoice_date);
    }


    /**
     * Get Brand label
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->nprcost, 'brand.label');
    }

    /**
     * Get Cost
     *
     * @return float
     */
    public function getCostAttribute()
    {
        return $this->nprcost->estimate;
    }

}
