<?php

namespace Betta\Services\Generator\Streams\FinancialProjection\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ExpensesRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Bind the implementation
     * This attributes will be used to return the sum of the costs
     * @var Array
     */
    protected $cost_attributes = [
        'Event FB',
        'Room Rental',
        'Room Rental - Unmet',
        'Audio Visual',
        'Invitations',
        'Shipping',
        'Speaker Air',
        'Speaker Hotel',
        'Speaker Ground Travel',
        'Speaker Expenses',
        'Speaker Hono',
    ];

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'ID',
        'Status',
        'Date',
        'Type',
        'Event FB',
        'Room Rental',
        'Room Rental - Unmet',
        'Audio Visual',
        'Invitations',
        'Shipping',
        'Speaker Air',
        'Speaker Hotel',
        'Speaker Ground Travel',
        'Speaker Expenses',
        'Speaker Hono',
        'Total Program Cost',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Get Program Id
     *
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Program Status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Get Program start date
     *
     * @return float
     */
    public function getDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Program Type
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return data_get($this->program, 'programType.label');
    }

    /**
     * Get Event FB expenses
     *
     * @return float
     */
    public function getEventFBAttribute()
    {
        return $this->program->fb_costs->sum('real');
    }

    /**
     * Get Room Rental expenses
     *
     * @return float
     */
    public function getRoomRentalAttribute()
    {
        return $this->program->room_rental_costs->sum('real');
    }

    /**
     * Get Room Rental - Unmet expenses
     *
     * @return float
     */
    public function getRoomRentalUnmetAttribute()
    {
        return $this->program->room_rental_unmet_costs->sum('real');
    }

    /**
     * Get Audio Visual expenses
     *
     * @return float
     */
    public function getAudioVisualAttribute()
    {
        return $this->program->av_category_costs->sum('real');
    }

    /**
     * Get Invitations cost expenses
     *
     * @return float
     */
    public function getInvitationsAttribute()
    {
        return $this->program->invite_costs->sum('real');
    }

    /**
     * Get Shipping expenses
     *
     * @return float
     */
    public function getShippingAttribute()
    {
        return $this->program->shipping_costs->sum('real');
    }

    /**
     * Get Speaker Air expenses
     *
     * @return float
     */
    public function getSpeakerAirAttribute()
    {
        return $this->program->travel_air_costs->sum('real');
    }

    /**
     * Get Speaker Hotel expenses
     *
     * @return float
     */
    public function getSpeakerHotelAttribute()
    {
        return $this->program->travel_hotel_costs->sum('real');
    }

    /**
     * Get Speaker Ground Travel expenses
     *
     * @return float
     */
    public function getSpeakerGroundTravelAttribute()
    {
        return $this->program->ground_transportation->sum('real')
                + $this->program->travel_car_costs->sum('real')
                + $this->program->travel_train_costs->sum('real');
    }

    /**
     * Get Speaker Expenses
     *
     * @return float
     */
    public function getSpeakerExpensesAttribute()
    {
        return $this->program->expense_speaker_costs->sum('real');
    }

    /**
     * Get Speaker Hono expenses
     *
     * @return float
     */
    public function getSpeakerHonoAttribute()
    {
        return $this->program->speaker_honorarium_costs->sum('real');
    }

    /**
     * Get Total Program Cost
     *
     * @return float
     */
    public function getTotalProgramCostAttribute()
    {
        return array_sum( array_only($this->attributes, $this->cost_attributes) );

    }
}
