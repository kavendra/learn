<?php

namespace Betta\Docusign\Resources\Envelope;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;

class EnvelopeService extends DocusignService
{
    /**
     * Bind the implementation
     *
     * @var Resource
     */
    public $envelopeResource;


    /**
    * Constructs the internal representation of the Docusign Envelope service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Construct the Service
        parent::__construct( $client );

        # return representation of Resource
        $this->envelopeResource = new EnvelopeResource( $this );
    }


    /**
     * Obtain Envelopes' statuses
     *
     * @param  array  $attributes
     * @return array
     */
    public function status( $attributes = array() )
    {
        $status =  $this->envelopeResource->status( $attributes );

        return object_get( $status,'envelopes', []);
    }


    /**
     * Get the Envelope
     *
     * @param  string $envelopeId
     * @return Object
     */
    public function get( $envelopeId )
    {
        return $this->envelopeResource->getEnvelope( $envelopeId );
    }


    /**
     * Return thr Recipients of the Evnelope
     *
     * @param  string $envelopeId
     * @return Object
     */
    public function recipients( $envelopeId )
    {
        return $this->envelopeResource->getEnvelopeRecipients( $envelopeId );
    }


    /**
     * Find the Recipient by CustomID
     *
     * @param  string $envelopeId
     * @param  string|int $clientUserId
     * @return Object
     */
    public function recipient( $envelopeId, $clientUserId, $group='signers' )
    {
        # Load recipients
        $recipients = $this->recipients( $envelopeId );

        # get the recipient's Group
        $group = data_get($recipients, $group, []);

        return collect($group)->where('clientUserId', $clientUserId)->first();
    }


    /**
     * Get the string representation of the Envelope Document
     *
     * @param  string $envelopeId
     * @param  string $documentId
     * @return String
     */
    public function document( $envelopeId, $documentId )
    {
        return $this->documents( $envelopeId, $documentId );
    }


    /**
     * Obtain the Array of Document from the envelope
     *
     * @param  string $envelopeId
     * @return Object
     */
    public function documents( $envelopeId, $documentId = '' )
    {
        return $this->envelopeResource->getEnvelopeDocuments( $envelopeId );
    }


    /**
     * Get combined documents of the envelope, potentailly, with certificate
     *
     * @param  string  $envelopeId
     * @param  boolean $certificate
     * @return string
     */
    public function documentsCombined(  $envelopeId, $certificate = true )
    {
        return $this->envelopeResource->getEnvelopeDocumentsCombined( $envelopeId , $certificate );
    }


    /**
     * Void the Envelope
     *
     * @param  string $envelopeId
     * @param  array $attributes
     * @return Object
     */
    public function void($envelopeId, $reason = 'System Voided')
    {
        return $this->envelopeResource->updateEnvelope($envelopeId, ['status'=>'voided', 'voidedReason'=>$reason]);
    }
}
