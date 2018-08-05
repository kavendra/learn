<?php

namespace Betta\Models\Traits;

trait ConfirmedByTrait
{
    /**
     * True if record is confirmed by field
     *
     * @return Relation
     */
    public function getIsConfirmedByFieldAttribute()
    {
        return $this->confirmed_by == $this->getConstant('CONFIRMED_BY_FIELD');
    }

    /**
     * True if record is confirmed by FLS
     *
     * @return Relation
     */
    public function getIsConfirmedByFlsAttribute()
    {
        return $this->confirmed_by == $this->getConstant('CONFIRMED_BY_FLS');
    }
}
