<?php

namespace Betta\Services\Generator\Streams;

use Carbon\Carbon;

trait CommonMergeFields
{
    /**
     * Current Date
     *
     * @return string | null
     */
    public function getCurrentDateAttribute()
    {
        return Carbon::today()->format(config('betta.long_date'));
    }

    /**
     * Support Phone
     *
     * @return string | null
     */
    public function getSupportPhoneAttribute()
    {
        return the_phone(config('fls.support_phone'));
    }

    /**
     * Support Phone
     *
     * @return string | null
     */
    public function getSupportEmailAttribute()
    {
        return config('fls.support_email');
    }

    /**
     * Application Client
     *
     * @return string | null
     */
    public function getClientNameAttribute()
    {
        return trans('app.name');
    }
}
