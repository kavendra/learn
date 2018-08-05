<?php

namespace Betta\Services\Distance;

use Betta\Models\Address;
use Betta\Models\PostalCode;
use Betta\Services\Distance\Providers\GoogleDistanceProvider;

class Distance
{
    /**
     * Distance provider
     *
     * @var string
     */
    protected $provider = GoogleDistanceProvider::class;

    /**
     * Zip in Radius
     *
     * @param  array|Address  $origin
     * @param  integer $radius
     * @param  string  $comparison
     * @return decimal
     */
    public function zipInRadius($origin, $radius = 10, $comparison = '<=')
    {
        $originZip = $this->getPostalCode($origin);

        # Get data
        return PostalCode::inRadius($originZip, $radius, $comparison)->get()->sortBy('distance');
    }

    /**
     * Calculate distance between two US Postal Codes, crow-flight'
     *
     * @param  Address|array $origin
     * @param  Address|array $destination
     * @return decimal
     */
    public function zipToZip($origin, $destination)
    {
        $originZip = $this->getPostalCode($origin);
        $destinationZip = $this->getPostalCode($destination);

        # Get data
        $data = PostalCode::zipToZip($originZip, $destinationZip)->first();

        return object_get($data, 'distance', 0);
    }

    /**
     * Use Provider to calculate the Distance between two points
     *
     * @param  Address|array $origin
     * @param  Address|array $destination
     * @param  Address|array $context
     * @return decimal
     */
    public function betweenAddresses($origin, $destination)
    {
        # Use Provider to resolve distnace
        $distance = $this->getProvider()->betweenAddresses($origin, $destination);

        #  Fall back to ZipToZip if the Resolver returns 'false'
        if ($distance === false){
            $distance = $this->zipToZip( $origin, $destination );
        }

        return floatval($distance);
    }

    /**
     * Get the check URL
     *
     * @param  Address|array $origin
     * @param  Address|array $destination
     * @return string
     */
    public function getCheckUrl($origin, $destination)
    {
        return $this->getProvider()->getCheckUrl($origin, $destination);
    }

    /**
     * Resolve Provider from container
     *
     * @return DistanceProvider
     */
    protected function getProvider()
    {
        return app()->make($this->provider);
    }

    /**
     * Get Postal Code from Address
     *
     * @param  Address|array $address
     * @return string
     */
    protected function getPostalCode($address)
    {
        return object_get($address, 'postal_code', array_get($address, 'postal_code', $address));
    }
}
