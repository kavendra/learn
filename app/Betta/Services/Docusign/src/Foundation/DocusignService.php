<?php

namespace Betta\Docusign\Foundation;

use Betta\Docusign\DocusignClient;

abstract class DocusignService
{

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
     * Class Contrcutor
     *
     * @param DocusignClient $client
     */
    public function __construct(DocusignClient $client)
    {
        $this->client = $client;
        $this->curl   = $client->getCUrl();
    }


    /**
     * Retrun DocusignClient
     *
     * @return Betta\Docusign\DocusignClient
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * Return Connection
     *
     * @return Betta\Docusign\Io\CurlIo
     */
    public function getCUrl()
    {
        return $this->curl;
    }
}
