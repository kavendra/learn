<?php

namespace Betta\Docusign\Resources\Status;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;

class StatusResource extends DocusignResource
{

    /**
     * Commonly shared date format for request
     *
     * @var string
     */
    protected $dateFormat='m/d/Y H:i';


    /**
     * Class constructor
     *
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        # Injuect Service
        parent::__construct( $service );
    }


    /**
     * Get Status
     * @param  int $fromDate
     * @param  string $status
     * @return Response
     */
    public function getStatus($fromDate, $status)
    {
        $date  = date($this->dateFormat, $fromDate);

        $url = $this->client->getBaseURL() . '/envelopes';

        $params = array(
            "from_date"      => $date,
            "from_to_status" => $status
        );

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders(), $params);
    }
}
