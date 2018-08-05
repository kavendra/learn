<?php

namespace Betta\Docusign\Resources\Status;

use Betta\Docusign\DocusignClient;
use NaeBettamo\Docusign\Foundation\DocusignService;

class StatusService extends DocusignService
{

    /**
     * Status Resource
     *
     * @var Betta\Docusign\Resources\Status\StatusResource
     */
    public $statusResource;


    /**
    * Constructs the internal representation of the Docusign Status service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Create the Client
        parent::__construct($client);

        # make proeprty a Resource
        $this->statusResource = new StatusResource( $this );
    }
}
