<?php

namespace Betta\Helpers;

use Illuminate\Support\Str;

class Strings
{
    /**
     * Generate a URL friendly "slug" without change to the case
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function ncSlug($title, $separator = '-')
    {
        $title = Str::ascii($title);
        # Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';
        # Clean up the title
        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);
        # Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', $title);
        # Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);
        # Resolve
        return trim($title, $separator);
    }

    /**
     * Return numbers only
     *
     * @param  mixed $string
     * @param  mixed $filler
     * @return string of numers
     */
    public static function numbersOnly($string, $filler = '')
    {
        return preg_replace('/[^0-9]/', $filler, $string);
    }
}
