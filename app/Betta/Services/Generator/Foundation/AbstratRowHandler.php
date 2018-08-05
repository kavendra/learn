<?php

namespace Betta\Services\Generator\Foundation;

use Betta\Foundation\Handlers\AbstractTransformer;

abstract class AbstratRowHandler extends AbstractTransformer
{
    /**
     * Format the String from the boolean
     *
     * @param  mixed $value
     * @return  string
     */
    protected function boolString($value, $yes='Yes', $no = 'No')
    {
        return $value ? $yes : $no;
    }

    /**
     * Resolve the keys of the Handler
     *
     * @return array
     */
    protected function keys()
    {
        return $this->keys;
    }

    /**
     * Make headers for the report
     *
     * @return array
     */
    public static function headers()
    {
        $keys = app(static::class)->keys();
        return array_map(create_function('$n', 'return null;'), array_flip($keys));

    }//end headers()
}
