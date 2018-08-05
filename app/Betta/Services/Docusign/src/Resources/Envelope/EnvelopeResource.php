<?php

namespace Betta\Docusign\Resources\Envelope;

use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;
use Betta\Docusign\Exceptions\BadEnvelopeException;

class EnvelopeResource extends DocusignResource
{

    /**
     * Format of the envelope string
     *
     * @var string
     */
    private $envelopeFormat = "/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/i";


    /**
     * Create instance of ENvelope Resource
     *
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        parent::__construct( $service );
    }


    /**
     * Get envelopes' statuses
     *
     * @param  array $attributes
     * @return array
     */
    public function status( $attributes )
    {
        $url = $this->client->makeBaseURL( ['envelopes'] );

        return $this->curl->get($url, $this->client->getHeaders(), $attributes );
    }


    /**
     * Get Envelope
     *
     * @param  string $envelopeId
     * @return Array
     */
    public function getEnvelope( $envelopeId )
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes', $envelopeId] );

        return $this->curl->get($url, $this->client->getHeaders() );
    }


    /**
     * Update Envelope
     *
     * @param  string $envelopeId
     * @return Array
     */
    public function updateEnvelope( $envelopeId, $attributes )
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes'] );

        return $this->curl->put($url, $this->client->getHeaders(), [], $attributes );
    }


    /**
     * Get Envelope Recipients
     *
     * @param  string $envelopeId
     * @return Response
     */
    public function getEnvelopeRecipients( $envelopeId )
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes', $envelopeId, 'recipients'] );

        return $this->curl->get( $url, $this->client->getHeaders() );
    }


    /**
     * Put Envelope Recipients
     *
     * @param  string $envelopeId
     * @return Response
     */
    public function putEnvelopeRecipients( $envelopeId, $recipients )
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes', $envelopeId, 'recipients'] );

        return $this->curl->put( $url, $this->client->getHeaders(), [], $recipients );
    }


    /**
     * Get Envelope Documents
     *
     * @param  string $envelopeId
     * @param  string|null $documentId
     * @return Response
     */
    public function getEnvelopeDocuments( $envelopeId, $documentId = '')
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes', $envelopeId, 'documents', $documentId] );

        return $this->curl->get( $url, $this->client->getHeaders() );
    }


    /**
     * Get all documents for the Enveloper
     *
     * @param  string $envelopeId
     * @param  boolean $certificate
     * @return Response
     */
    public function getEnvelopeDocumentsCombined( $envelopeId, $certificate = true )
    {
        $this->checkEnvelopeId( $envelopeId );

        $url = $this->client->makeBaseURL( ['envelopes', $envelopeId, 'documents', 'combined'] ) ;

        $params = ( (bool) $certificate === true )
                            ? array( 'certificate' => 'true' )
                            : array( 'certificate' => 'false' );

        return $this->curl->get( $url,
                        $this->client->getHeaders('Accept: application/pdf', 'Content-Type: application/pdf'),
                        $params );
    }


    /**
     * check Envelope ID
     *
     * @param  mixed $envelopeId
     * @return boolean|Exception
     */
    private function checkEnvelopeId( $envelopeId )
    {
        if ( $this->isInvalidEnvelopeId($envelopeId) ){
            # throw an exception
            throw new BadEnvelopeException();
        }

        return true;
    }


    /**
     * Validate EnvelopeID, true if valid
     *
     * @param  string $envelopeId
     * @return boolean
     */
    private function isValidEnvelopeId( $envelopeId )
    {
        return preg_match( $this->envelopeFormat ,(string) $envelopeId) === 1;
    }


    /**
     * True if the provided envelopeId is NOT valid
     *
     * @param  string $envelopeId
     * @return boolean
     */
    private function isInvalidEnvelopeId($envelopeId)
    {
        return !$this->isValidEnvelopeId($envelopeId);
    }
}
