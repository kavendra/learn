<?php

namespace Betta\Services\Generator\Foundation;

use Betta\Helpers\Strings;

trait SanitizesStrings
{
    /**
     * Sanitize a string to be a valid filename
     *
     * @param  string $string
     * @param  string $filler
     * @return string
     */
    protected function sanitizeFileName($string, $filler = ' ')
    {
        return Strings::ncSlug($string, $filler);
    }
}
