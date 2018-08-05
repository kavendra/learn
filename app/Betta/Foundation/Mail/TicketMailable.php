<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Ticket;

abstract class TicketMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Ticket
     */
    public $ticket;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'ticket';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
