<?php

namespace Betta\Docusign\Resources\Views;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;

class ViewsService extends DocusignService
{

    /**
     * Views Resource
     *
     * @var Betta\Docusign\Resources\Views\ViewsResource
     */
    public $viewsResource;


    /**
    * Constructs the internal representation of the DocuSign Views service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Create the Client
        parent::__construct($client);

        # make property a Resource
        $this->viewsResource = new ViewsResource( $this );
    }


    /**
     * Translate request from Service to Resource for the Recipient View
     *
     * @param  string $returnUrl
     * @param  string $envelopeId
     * @param  string $userId
     * @param  mixed  $clientUserId
     * @param  string $authMethod
     * @return Object
     */
    public function getRecipientView( $returnUrl, $envelopeId, $userId, $clientUserId = NULL, $authMethod = 'email' )
    {
        return $this->viewsResource->getRecipientView($returnUrl, $envelopeId, $userId, $clientUserId, $authMethod );
    }

    /**
     * Return Sender View
     *
     * @param  string $returnUrl
     * @param  string $envelopeId
     * @return Object
     */
    public function getSenderView( $returnUrl, $envelopeId )
    {
        return $this->viewsResource->getSenderView( $returnUrl, $envelopeId );
    }


    /**
     * Return the URL for Admin
     *
     * @return Object
     */
    public function getConsoleView()
    {
        return $this->viewsResource->getConsoleView();
    }
}
