<?php
namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\NonProgramRelatedCosts;

use Betta\Models\NprCost;
use Betta\Services\Generator\Foundation\AbstratRowHandler as RowHandler;

class Handler extends RowHandler
{
    /**
     * Columns
     *
     * @var Array
     */
    protected $keys = [
        'Brand',
        'Date',
        'Cost Item',
        'Estimate',
    ];

    /**
     * Helper values that should not be visible in resulting array
     *
     * @var array
     */
    protected $hidden = [
        'invoice_date'
    ];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\NprCost
     */
    protected $nprCost;

    /**
     * Create new class
     *
     * @param Cost $cost
     */
    public function __construct(NprCost $nprCost)
    {
        $this->nprCost = $nprCost;
    }

    /**
     * Get Context Type
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->nprCost,'brand.label');
    }

    /**
     * Get Context ID
     *
     * @return string
     */
    public function getDateAttribute()
    {
        return excel_date($this->nprCost->invoice_date);
    }

    /**
     * Get Payee, if present
     *
     * @return string
     */
    public function getCostItemAttribute()
    {
        return data_get($this->nprCost,'costItem.label');
    }

    /**
     * Get estimated cost
     *
     * @return string
     */
    public function getEstimateAttribute()
    {
        return (float)$this->nprCost->estimate;
    }

    /**
     * Get the Invoice Date of the transformed model and hide it
     *
     * @return Carbon
     */
    public function getInvoiceDateAttribute()
    {
        return $this->nprCost->invoice_date;
    }
}
