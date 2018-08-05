<?php

namespace Betta\Foundation\Events;

use Betta\Models\Document;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractAttachedContextEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Document
     */
    public $document;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    public $context;

    /**
     * Bind the implementation
     *
     * @var String
     */
    public $reference_name;

    /**
     * Create a new event instance.
     *
     * @param  Betta\Models\Document $document
     * @param  Illuminate\Database\Eloquent\Model $context
     * @param  String $reference_name
     * @return void
     */
    public function __construct(Document $document, Model $context, $reference_name = null)
    {
        $this->document = $document;
        $this->context = $context;
        $this->reference_name = $reference_name;
    }
}
