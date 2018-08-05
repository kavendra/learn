<?php

namespace Betta\Services\Distance\Providers;

use Illuminate\Contracts\Support\Arrayable;
use Betta\Services\Distance\Interfaces\SanitizeAddressInterface;
use Betta\Services\Distance\Interfaces\DistanceProviderInterface;

class GoogleDistanceProvider implements SanitizeAddressInterface, DistanceProviderInterface
{
    /**
     * Google API Key
     *
     * @var string
     */
    protected $apiKey = '';
    /**
     * Base URL to check
     *
     * @var string
     */
    protected $distanceUrl = 'https://maps.googleapis.com/maps/api/distancematrix/xml?sensor=false';

    /**
     * Directions URL
     *
     * @var string
     */
    protected $checkUrl = 'https://www.google.com/maps/dir/';

    /**
     * Default Factor to convert to miles
     *
     * @var float
     */
    protected $unitFactor = 0.000621371;

    /**
     * Only elements of address that we need
     *
     * @var array
     */
    protected $elements = [
        'line_1',
        'city',
        'state_province',
        'postal_code',
    ];

    /**
     * Create New instance of Distance Provider
     *
     * @return Void
     */
    public function __construct()
    {
        $this->apiKey = config('services.google_map_api.key');
    }

    /**
     * Use Provider to calculate the Distance between two points
     *
     * @param  Address|array $origin
     * @param  Address|array $destination
     * @return decimal
     */
    public function betweenAddresses($origin, $destination)
    {
        $url = $this->getDistanceUrl($origin, $destination);

        # maybe Guzzle it?
        # @todo: leverage GuzzleHttp library to CURL
        try {
            $xml = simplexml_load_file($url);
        } catch (\Exception $e) {
            # Log error
            logger()->error('Google DistanceMatrixAPI returned with error', [
                'url' => $url,
                'origin' => $origin,
                'destination' => $destination,
                'error' => $e->getMessage()
            ]);
            # False return
            return false;
        }
        # gather value,
        $value = object_get($xml, 'row.element.distance.value');
        # Return
        return $value ? $value * $this->unitFactor : false;
    }

    /**
     * Get the distance URL
     *
     * @param  Address|array $origin
     * @param  Address|array  $destination
     * @return string
     */
    public function getDistanceUrl($origin, $destination)
    {
        # Sanitize Addreses:Source
        $origin = $this->sanitizeAddress($origin);
        # Sanitize Addreses:Destination
        $destination  = $this->sanitizeAddress($destination);
        # Resolve
        return str_replace(
            ' ',
            '+',
            "{$this->distanceUrl}&origins={$origin}&destinations={$destination}&key={$this->apiKey}"
        );
    }

    /**
     * Get the public check distance URL
     *
     * @param  Address|array $origin
     * @param  Address|array  $destination
     * @return string
     */
    public function getCheckUrl($origin, $destination)
    {
        # Sanitize Addreses:Source
        $origin = $this->sanitizeAddress($origin);
        # Sanitize Addreses:Destination
        $destination  = $this->sanitizeAddress($destination);
        # Resolve
        return str_replace(' ', '+', "{$this->checkUrl}{$origin}/{$destination}");
    }

    /**
     * Clean up address
     *
     * @param  Arrayable|array $address
     * @return string
     */
    public function sanitizeAddress($address)
    {
        if ($address instanceOf Arrayable){
            $address = $address->toArray();
        }
        $address = array_only($address, $this->elements);
        # Convert to String
        $address = implode(' ', $address);
        # sanitize
        return preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $address);
    }
}
