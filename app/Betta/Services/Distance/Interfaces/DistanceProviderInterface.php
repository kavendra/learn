<?php

namespace Betta\Services\Distance\Interfaces;

interface DistanceProviderInterface
{
    /**
     * Resolve distance between two addresses
     *
     * @param  Address|array $origin
     * @param  Address|array $destination
     * @return decimal
     */
    function betweenAddresses($origin, $destination);
}
