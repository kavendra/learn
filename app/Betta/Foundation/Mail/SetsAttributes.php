<?php

namespace Betta\Foundation\Mail;

trait SetsAttributes
{
    /**
     * Set the Template ID
     *
     * @param int $value
     * @return $this
     */
    public function setTemplateId($value)
    {
        $this->template_id = $value;
        # chainable
        return $this;
    }
}
