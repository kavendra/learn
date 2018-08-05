<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait Brands
{
    /**
     * Get the value of the Label for the Brand, if exists
     *
     * @param  $offset
     * @return string
     */
    protected function getBrandLabelAtOffset($offset, $default = null)
    {
        if($this->brands->offsetExists($offset)){
            return $this->brands->offsetGet($offset)->label;
        }

        return $default;
    }

    /**
     * Label of the Brand at the 0 Offset
     *
     * @return string | null
     */
    public function getSpendProduct1OrTherapeuticAreaAttribute()
    {
        return $this->getBrandLabelAtOffset(0);
    }

    /**
     * Label of the Brand at the 1 Offset (2nd for human)
     *
     * @return string | null
     */
    public function getSpendProduct2OrTherapeuticAreaAttribute()
    {
        return $this->getBrandLabelAtOffset(1);
    }

    /**
     * Label of the Brand at the 2 Offset (3rd for human)
     *
     * @return string | null
     */
    public function getSpendProduct3OrTherapeuticAreaAttribute()
    {
        return $this->getBrandLabelAtOffset(2);
    }

    /**
     * Label of the Brand at the 3 Offset (4th for human)
     *
     * @return string | null
     */
    public function getSpendProduct4OrTherapeuticAreaAttribute()
    {
        return $this->getBrandLabelAtOffset(3);
    }

    /**
     * Label of the Brand at the 4 Offset (5th for human)
     *
     * @return string | null
     */
    public function getSpendProduct5OrTherapeuticAreaAttribute()
    {
        return $this->getBrandLabelAtOffset(4);
    }
}
