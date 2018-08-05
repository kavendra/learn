<?php

namespace Betta\Foundation\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class AbstractMailable extends Mailable implements ShouldQueue
{
    /**
     * Note that we were able to pass an Eloquent model directly into the constructor.
     * Because of the SerializesModels trait that the job is using, Eloquent models
     * will be gracefully serialized and unserialized when the job is processing.
     * If your queued job accepts an Eloquent model in its constructor, only
     * the identifier for the model will be serialized onto the queue.
     *
     * ***
     * *** THE QUEUE SYSTEM WILL
     * *** AUTOMATICALLY RE-RETRIEVE
     * *** THE FULL MODEL INSTANCE FROM THE DATABASE
     * ***
     */
    use Queueable, SerializesModels;

    /**
     * Abstract mailable can set various attributes
     */
    use SetsAttributes;
}
