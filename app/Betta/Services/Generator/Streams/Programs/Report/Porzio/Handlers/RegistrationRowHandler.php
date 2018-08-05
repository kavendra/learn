<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

use Betta\Models\Program;
use Betta\Models\Registration;

class RegistrationRowHandler extends AbstractPorzioRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Registration
     */
    protected $registration;

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
     * @param Registration $registration
     * @param Program      $program
     */
    public function __construct(Registration $registration, Program $program)
    {
        $this->program = $program;
        $this->registration = $registration;
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
     * Resolve Expense ID
     *
     * @return integer
     */
    public function getExpenseIdAttribute()
    {
        return "R-{$this->registration->id}";
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
     * Spend Entry is the Date when Registration is created
     *
     * @return string
     */
    public function getSpendEntryDateAttribute()
    {
        return $this->registration->created_at->format($this->dateFormat);
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
     * For Registrations the Spend Purpose is Food, but it is specific to bucket
     *
     * @return String
     */
    public function getSpendPurposePrimaryAttribute()
    {
        switch ($this->registration->registration_bucket){
            case Registration::SPEAKER:
                $value = 'SPEAKER MEAL';
                break;
            default:
                $value = 'EVENT MEAL';
                break;
        }

        return $value;
    }

    /**
     * Secondary Purpose based on bucket
     *
     * @return null
     */
    public function getSpendPurposeSecondaryAttribute()
    {
        switch ($this->registration->registration_bucket){
            case Registration::SPEAKER:
                $value = 'SPEAKER';
                break;
            default:
                $value = 'ATTENDEE';
                break;
        }

        return $value;
    }

    /**
     * FB Per Person
     *
     * @return float
     */
    public function getSpendAmountProRataAttribute()
    {
        return number_format($this->program->fb_per_person,  2, '.', '');
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
     * @return Collection
     */
    public function getRecipientAttribute()
    {
        return $this->registration;
    }

    /**
     * Resolve Recipient
     *
     * @access hidden
     * @return Collection
     */
    public function getRecipientAddressAttribute()
    {
        return $this->registration;
    }
}
