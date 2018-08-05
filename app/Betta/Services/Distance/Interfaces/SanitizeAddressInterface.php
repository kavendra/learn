<?php

namespace Betta\Services\Distance\Interfaces;

interface SanitizeAddressInterface
{
    /**
     * Clean up the attribute , return sanitized string
     *
     * @param  Address|array $address
     * @return string
     */
    function sanitizeAddress($address);
}
