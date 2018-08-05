<?php

namespace Betta\Docusign\Resources\Signature;

use Betta\Docusign\DocusignClient;
use Illuminate\Filesystem\Filesystem;
use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Exceptions\SignatureException;

class SignatureService extends DocusignService
{

    /**
     * Signature Resource
     *
     * @var Betta\Docusign\Resources\Signature\SignatureResource
     */
    public $signatureResource;


    /**
     * Bind the implementation
     *
     * @var
     */
    protected $file;


    /**
    * Constructs the internal representation of the Docusign RequestSignature service.
    *
    * @param DocusignClient $client
    */
    public function __construct( DocusignClient $client )
    {
        # Create the Client
        parent::__construct($client);

        # make proeprty a Resource
        $this->signatureResource = new SignatureResource( $this );

        # Filesystem implementation is shared,
        # so we can resolve it directly from the IoC
        $this->file = app()->make('Illuminate\Filesystem\Filesystem');
    }


    /**
     * Interface into Resource
     *
     * @param  string $emailSubject
     * @param  string $emailBlurb
     * @param  string $status
     * @param  array  $documents of DocusignDocuments
     * @param  array  $recipients
     * @param  array  $eventNotifications
     * @param  array  $options
     * @return Response
     */
    public function request( $emailSubject
                            , $emailBlurb
                            , $status = "created"
                            , $documents = array()
                            , $recipients = array()
                            , $eventNotifications = array()
                            , $options = array() ) {

        return $this->signatureResource
                    ->createEnvelopeFromDocument( $emailSubject,
                        $emailBlurb,
                        $status,
                        $this->processDocuments( $documents ),
                        $this->processRecipients( $recipients ),
                        $eventNotifications,
                        $options );
    }


    /**
     * Build the Envelope from the Template, in Draft status
     *
     * @param  string $emailSubject
     * @param  string $emailBlurb
     * @param  string $templateId
     * @param  string $status
     * @param  array  $templateRoles
     * @param  array  $eventNotifications
     * @return Request
     */
    public function requestFromTemplate( $emailSubject
                                         , $emailBlurb
                                         , $templateId
                                         , $status = "created"
                                         , $templateRoles = array()
                                         , $eventNotifications = array() ) {

    }


    /**
     * Process the Document
     *
     * @param  array  $document
     * @return Array
     */
    private function processDocuments($documents = array() )
    {
        foreach ($documents as $id => &$document){
            # Clone into a temp value
            $temp     = $document;

            # Create new Document
            $document = new Document();

            # assign all values
            $document->setId( $id + 1 )
                             ->setName( array_get( $temp,'name') )
                             ->setContent( $this->readFile( array_get($temp,'path') ) );
        }

        return $documents;
    }



    /**
     * Process the Recipients
     *
     * @param  array  $recipients
     * @return Array
     */
    private function processRecipients( $recipients = array() )
    {
        # Iterator
        $i = 1;

        foreach ($recipients as &$recipient){
            # process array and make Recipient
            $recipient = $this->processRecipient( $recipient, $i );

            # iterate
            $i++;
        }

        return $recipients;
    }


    /**
     * Process a single Recipient record
     *
     * @param  array $recipient
     * @return Betta\Docusign\Resources\Signature\Recipient
     */
    private function processRecipient( $recipientArray, $id )
    {
        # Init
        $recipient = new Recipient();
        # Compile
        $recipient->setRoutingOrder( array_get($recipientArray, 'routingOrder', $id) )
                            ->setId( array_get($recipientArray, 'recipientId', $id) )
                            ->setName( array_get($recipientArray, 'name', '') )
                            ->setEmail( array_get($recipientArray, 'email', '') )
                            ->setClientId( array_get($recipientArray, 'clientUserId', null))
                            ->setType( array_get($recipientArray, 'type', 'signers') );

        # Also, assign tabs
        foreach (array_get($recipientArray, 'tabs', []) as $tabType => $tab){
            # Type    # Where to get the tab from in Recipient Array
            $recipient->setTab( $tabType, $tab );
        }

        return $recipient;
    }


    /**
     * Read the file as a string
     *
     * @param  string $path
     * @return string
     */
    private function readFile($path)
    {
        if ( $this->file->exists($path) ){
            return $this->file->get($path);
        }

        throw new SignatureException('File not Found');
    }
}
