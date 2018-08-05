<?php

namespace Betta\Docusign\Foundation;

use Betta\Docusign\Foundation\DocusignService;

abstract class DocusignResource
{

    /**
     * Share the DocusignService accorss
     *
     * @var Betta\Docusign\Foundation\DocusignService
     */
    protected $service;


    /**
     * Share the DocusignClient
     *
     * @var Betta\Docusign\DocusignClient
     */
    protected $client;


    /**
     * Share the Docusign connection
     *
     * @var Betta\Docusign\Io\CurlIo
     */
    protected $curl;


    /**
     * Class constructor
     *
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        $this->service = $service;
        $this->client  = $service->getClient();
        $this->curl    = $service->getCUrl();
    }
}
