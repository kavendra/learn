<?php

namespace Betta\Foundation\Events;

use Betta\Models\Note;

abstract class AbstractNoteEvent extends AbstractBettaEvent
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Note
     */
    public $note;

    /**
     * Create a new event instance.
     *
     * @param Note $note
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
    }
}
