<?php

namespace Betta\Services\Generator\Streams\Request;

use Carbon\Carbon;
use Betta\Models\Engagement;

trait MergesRequestData
{
    /**
     * Use container Request  to solve the manager
     *
     * @return string | null
     */
    public function getRequestManagerNameAttribute()
    {
        return data_get($this->request, 'primary_coordinator.preferred_name');
    }

    /**
     * Use container Request  to solve the manager' email
     *
     * @return string | null
     */
    public function getRequestManagerEmailAttribute()
    {
        return data_get($this->request, 'primary_coordinator.email');
    }

    /**
     * Use container Request  to solve the manager' email
     *
     * @return string | null
     */
    public function getRequestManagerPhoneAttribute()
    {
        return data_get($this->request, 'primary_coordinator.phone');
    }
}
