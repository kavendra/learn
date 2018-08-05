<?php

namespace Betta\Models\Observers;

use Betta\Models\Note;
use Betta\Foundation\Eloquent\AbstractObserver;

class NoteObserver extends AbstractObserver
{
    /**
     * Notifyable Models
     *
     * @var Array
     */
    protected $events = [
        'Betta\Models\Ticket' => 'App\Events\Note\TicketNoteCreated',
        'Betta\Anomaly\Models\ProcessException' => 'Betta\Anomaly\Events\ProcessExceptionNoteCreated',
    ];

    /**
     * Listen to the Note creating event.
     *
     * @param  Note  $model
     * @return void
     */
    public function creating(Note $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Note created event.
     *
     * @param  Note  $model
     * @return void
     */
    public function created(Note $model)
    {
        $this->fireContextEvents($model);
    }

    /**
     * Fire the Event if the context matches
     * {$model->context_type}\NoteCreated
     *
     * @param  Note   $model
     * @return Void
     */
    protected function fireContextEvents(Note $model)
    {
        if ($event = $this->getEvents($model)){
            # this event will receive a Note, not the context;
            return event(new $event($model));
        }
    }

    /**
     * Try to obtain the event for the context
     *
     * @param  Note   $model
     * @return string | null
     */
    protected function getEvents(Note $model)
    {
        return array_get($this->events, $model->context_type);
    }
}
