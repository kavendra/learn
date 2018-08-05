<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait Field
{
        /**
     * Field Representative: CMID
     *
     * @return string|null
     */
    public function getSalesRepIdAttribute()
    {
        return null;
    }

    /**
     * Field Representative: Last Name
     *
     * @return string
     */
    public function getSalesRepLastNameAttribute()
    {
        return data_get($this->field, 'last_name');
    }

    /**
     * Field Representative: First Name
     *
     * @return string
     */
    public function getSalesRepFirstNameAttribute()
    {
        return data_get($this->field, 'first_name');
    }
}
