<?php

namespace Betta\Services\Generator\Streams\Ticket\Master\Handlers;

use Betta\Models\Ticket;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class TicketRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Ticket
     */
    protected $ticket;

    /**
     * Count to campare against
     *
     * @var int
     */
    protected $count;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'id',
        'brands',
        'label',
        'category',
        'description',
        'programs',
        'status',
        'created_at',
        'due_at',
        'created_by',
        'assigned_to',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Create new Row instance
     *
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket, $count = 0)
    {
        $this->ticket = $ticket;
        $this->count = $count;
    }

    /**
     * Ticket ID
     *
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->ticket->id;
    }

    /**
     * Labels of brands involved
     *
     * @return string
     */
    public function getBrandsAttribute()
    {
        # Compare
        if( $this->count == $this->ticket->brands->count() ){
            return 'All Brands';
        }
        # resolve
        return $this->ticket->brands->implode('label', ' | ');
    }

    /**
     * Resolve Label from Ticket
     *
     * @return string
     */
    public function getLabelAttribute()
    {
        return $this->ticket->label;
    }

    /**
     * Resolve categories
     *
     * @return string
     */
    public function getCategoryAttribute()
    {
        return data_get($this->ticket, 'ticketCategory.label');
    }

    /**
     * Resolve the Date
     *
     * @return float
     */
    public function getDueAtAttribute()
    {
        return excel_date($this->ticket->due_date);
    }

    /**
     * Resolve categories
     *
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->ticket->action_resolution;
    }

    /**
     * Name of Affected Programs
     *
     * @return string
     */
    public function getProgramsAttribute()
    {
        return $this->ticket->programs_affected;
    }

    /**
     * Label from ticket status
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return data_get($this->ticket, 'ticketStatus.label');
    }

    /**
     * Resolve the Date
     *
     * @return float
     */
    public function getCreatedAtAttribute()
    {
        return excel_date($this->ticket->created_at);
    }

    /**
     * Name of Ticket Creator
     *
     * @return string
     */
    public function getCreatedByAttribute()
    {
        return data_get($this->ticket, 'createdBy.preferred_name');
    }

    /**
     * Names of profiles on which Ticket Assigned to
     *
     * @return string
     */
    public function getAssignedToAttribute()
    {
        return $this->ticket->profiles->implode('preferred_name', ', ');
    }

}

