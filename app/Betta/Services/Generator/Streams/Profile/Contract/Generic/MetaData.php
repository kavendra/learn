<?php

namespace Betta\Services\Generator\Streams\Profile\Contract\Generic;

use Carbon\Carbon;
use Betta\Models\Contract;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

trait MetaData
{
    /**
     * Resolve From meta: work_to_perform
     *
     * @return string
     */
    public function getWorkToPerformAttribute()
    {
        return data_get($this->contract, 'keyed_metas.work_to_perform', '');
    }

    /**
     * Resolve From meta: bg_technology
     *
     * @return string
     */
    public function getBackgroundTechnologyAttribute()
    {
        return data_get($this->contract, 'keyed_metas.bg_technology', '');
    }

    /**
     * Resolve From meta: other_terms
     *
     * @return string
     */
    public function getOtherTermsAttribute()
    {
        return data_get($this->contract, 'keyed_metas.other_terms', '');
    }

    /**
     * Resolve From meta: other_terms
     *
     * @return string
     */
    public function getExpiryDateAttribute()
    {
        if($value = data_get($this->contract, 'keyed_metas.expiry_date')){
            return Carbon::parse($value)->format($this->dateFormat);
        }
    }
}
