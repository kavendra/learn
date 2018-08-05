<?php

namespace Betta\Services\Generator\Streams\Shared;

use Carbon\Carbon;

trait CommonMerges
{
    /**
     * Get currency Symbol
     *
     * @return string
     */
    public function getUsdAttribute()
    {
        return '$';
    }

    /**
     * Get currency Symbol
     *
     * @return string
     */
    public function getCurrencyAttribute()
    {
        return $this->usd;
    }

    /**
     * Current Date, formatted as long
     *
     * @return string
     */
    public function getCurrentDateAttribute()
    {
        return Carbon::today()->format(config('betta.long_date'));
    }

    /**
     * Resolve Support Phone
     *
     * @return string
     */
    public function getSupportPhoneAttribute()
    {
        return config('fls.support_phone');
    }

    /**
     * Resolve Support Email
     *
     * @return string
     */
    public function getSupportEmailAttribute()
    {
        return config('fls.support_email');
    }

    /**
     * Resolve Support Email
     *
     * @return string
     */
    public function getSupportFasAttribute()
    {
        return config('fls.support_fax');
    }
}
