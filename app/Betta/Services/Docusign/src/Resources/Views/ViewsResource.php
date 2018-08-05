<?php

namespace Betta\Docusign\Resources\Views;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;

class ViewsResource extends DocusignResource
{

    /**
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        # Inject Service
        parent::__construct($service);
    }


    /**
     * Get Contsole View (this is for those who have account with DS)
     *
     * @return Response
     */
    public function getConsoleView()
    {
        $url = $this->client->makeBaseUrl( ['views', 'console'] );

        return $this->curl->post( $url, $this->client->getHeaders() );
    }


    /**
     * Set Sender View
     *
     * @param  string $returnUrl
     * @param  string $envelopeId
     * @return Response
     */
    public function getSenderView( $returnUrl, $envelopeId )
    {
        $url = $this->client->makeBaseUrl( ['envelopes', $envelopeId, 'views','sender'] );

        $data = ['returnUrl' => $returnUrl];

        return $this->curl->post( $url, $this->client->getHeaders(), array(), json_encode($data) );
    }

    /**
     * Get Recipient View
     *
     * @param  string $returnUrl
     * @param  string $envelopeId
     * @param  string $userId
     * @param  mixed  $clientUserId
     * @param  string $authMethod
     * @return Response
     */
    public function getRecipientView( $returnUrl, $envelopeId, $userId, $clientUserId = NULL, $authMethod = 'email' )
    {
        $url = $this->client->makeBaseUrl( ['envelopes', $envelopeId, 'views','recipient'] );

        $data = array (
            'clientUserId'         => $clientUserId,
            'userId'               => $userId,
            'returnUrl'            => $returnUrl,
            'authenticationMethod' => $authMethod,
        );

        return $this->curl->post( $url, $this->client->getHeaders(), array(), json_encode($data) );
    }
}
