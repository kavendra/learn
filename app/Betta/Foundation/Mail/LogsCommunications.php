<?php

namespace Betta\Foundation\Mail;

trait LogsCommunications
{
    /**
     * List the Recipients
     *
     * @var array
     */
    protected $recipients = [];

    /**
     * Enable History by setting context and Recipients
     *
     * @return $this
     */
    protected function log()
    {
        return $this->withContext()->withRecipients()->withTemplate();
    }

    /**
     * Set the context
     *
     * @return $this
     */
    protected function withContext()
    {
        $this->callbacks[] = function ($message){
            $message->context = data_get($this, $this->context);
        };

        return $this;
    }

    /**
     * Add all of the recipients to the message.
     *
     * @return $this
     */
    protected function withRecipients()
    {
        $this->callbacks[] = function ($message){
            $message->recipients = $this->recipients;
        };

        return $this;
    }

    /**
     * Set the Sending Template, if provided
     *
     * @return $this
     */
    protected function withTemplate()
    {
        if (!empty($this->template_id)){
            # add anonymous function setting the Template as a Context
            $this->callbacks[] = function ($message) {
                $message->template = $this->template_id;
            };
        }

        return $this;
    }

    /**
     * Parse the given user into an object.
     *
     * @deprecated Laravel 5.5
     * @todo  remove once we move to 5.5
     * @see    Illuminate\Mail\Mailable
     * @param  mixed  $user
     * @return object
     */
    protected function parseUser($user)
    {
        # Push the Recipient into the array
        $this->recipients[] = $user;
        # Fall back to the Mailable
        return parent::parseUser($user);
    }

    /**
     * Convert the given recipient into an object.
     *
     * @see    Illuminate\Mail\Mailable
     * @param  mixed  $recipient
     * @return object
    */
    protected function normalizeRecipient($recipient)
    {
        # Push the Recipient into the array
        $this->recipients[] = $recipient;
        # Fall back to the Mailable
        return parent::normalizeRecipient($recipient);
    }
}
