<?php

namespace Betta\Foundation\Helpers;

use Carbon\Carbon;

class StringCompiler
{
    /**
     * Pattern to use for the placeholder replacement
     *
     * @var string
     */
    protected static $pattern = '|%.*?%|';

    /**
     * Compile the string
     *
     * @param  string $string
     * @return string
     */
    public static function compile($string)
    {
        # Match the parts
        preg_match_all(static::$pattern, $string, $parts);

        foreach (head($parts) as $replacable) {
            $string = str_replace($replacable, static::$replacable(), $string);
        }

        return $string;
    }

    /**
     * Call the repalcing method
     *
     * @param  method $name
     * @param  mixed $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        # sanitize method name
        $method = str_replace('%', '', $method);
        # return
        return static::$method($arguments);
    }

    /**
     * Return Year
     *
     * @return integer
     */
    protected static function year()
    {
        return Carbon::today()->year;
    }

    /**
     * Return month number (January = 01)
     *
     * @return string
     */
    protected static function month()
    {
        return str_pad(Carbon::today()->month, 2, "0", STR_PAD_LEFT);
    }

    /**
     * Return day number (1 = 01)
     *
     * @return string
     */
    protected static function day()
    {
        return str_pad(Carbon::today()->day, 2, "0", STR_PAD_LEFT);
    }
}
