<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

use Betta\Models\Cost;
use Betta\Models\Program;

class CostRowHandler extends AbstractPorzioRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Cost
     */
    protected $cost;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
        'location',
        'brands',
        'field',
        'recipient',
        'recipient_address',
    ];

    /**
     * Create new Row instance
     *
     * @param Cost    $cost
     * @param Program $program
     */
    public function __construct(Cost $cost, Program $program)
    {
        $this->cost = $cost;
        $this->program = $program;
    }

    /**
     * Resolve the Program ID
     *
     * @return integer
     */
    public function getEventIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Resolve Expense ID as Cost ID
     *
     * @return integer
     */
    public function getExpenseIdAttribute()
    {
        return "C-{$this->cost->id}";
    }

    /**
     * Resolve Spend Date attribute
     *
     * @return string
     */
    public function getSpendDateAttribute()
    {
        return $this->program->start_date->format($this->dateFormat);
    }

    /**
     * Spend Entry is the Date when Cost is created
     *
     * @return string
     */
    public function getSpendEntryDateAttribute()
    {
        return $this->cost->created_at->format($this->dateFormat);
    }

    /**
     * Resolve the Address from
     *
     * @access hidden
     * @return \Betta\Models\Address | null
     */
    public function getLocationAttribute()
    {
        return $this->program->address;
    }

    /**
     * Enumerated Value based on the location
     *
     * @return string
     */
    public function getSpendLocationOrDestinationTypeAttribute()
    {
        return $this->program->is_onsite ? 'Office' : 'Venue';
    }

    /**
     * Resolve Brands from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getBrandsAttribute()
    {
        return $this->program
                    ->brands
                    ->sortByDesc('pivot.is_primary')
                    ->values();
    }

    /**
     * For Cost Categories
     *
     * @return String
     */
    public function getSpendPurposePrimaryAttribute()
    {
        return data_get($this->cost->categories->first(), 'pivot.porzio_label', '--NOT-SET');
    }

    /**
     * Secondary Purpose based on bucket
     *
     * @return null
     */
    public function getSpendPurposeSecondaryAttribute()
    {
        return "SPEAKER";
    }

    /**
     * FB Per Person
     *
     * @return float
     */
    public function getSpendAmountProRataAttribute()
    {
        return number_format($this->cost->real,  2, '.', '');
    }

    /**
     * Alias for FB Per Person
     *
     * @return float
     */
    public function getSpendAmountTotalCostAttribute()
    {
        return $this->getSpendAmountProRataAttribute();
    }

    /**
     * Resolve Primary Field from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getFieldAttribute()
    {
        return $this->program->primary_field;
    }

    /**
     * Resolve Recipient
     *
     * @access hidden
     * @return Profile
     */
    public function getRecipientAttribute()
    {
        return object_get($this->cost,'payee.profile');
    }

    /**
     * Resolve Recipient
     *
     * @access hidden
     * @return Collection|null
     */
    public function getRecipientAddressAttribute()
    {
        return data_get($this->recipient, 'preferred_address');
    }
}
