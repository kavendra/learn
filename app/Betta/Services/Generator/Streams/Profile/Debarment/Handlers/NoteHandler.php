<?php

namespace Betta\Services\Generator\Streams\Profile\Debarment\Handlers;

use Betta\Models\Note;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class NoteHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Note
     */
    protected $note;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'content',
        'note_at',
        'note_by',
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
     * @param Betta\Models\Note $note
     */
    public function __construct(Note $note)
    {
        $this->note = $note;
    }

    /**
     * Make the Note Date
     *
     * @return string
     */
    protected function getNoteAtAttribute()
    {
        if($date = data_get($this->note, 'created_at')){
            return $date->format( config('betta.short_date') );
        }

        return null;
    }

    /**
     * Note Author
     *
     * @return string | null
     */
    protected function getNoteByAttribute()
    {
        return data_get($this->note->createdBy, 'preferred_name');
    }

    /**
     * Note content
     *
     * @return string
     */
    protected function getContentAttribute()
    {
        return $this->note->content;
    }
}
