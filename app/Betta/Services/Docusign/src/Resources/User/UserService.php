<?php

namespace Betta\Docusign\Resources\User;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;

class UserService extends DocusignService
{

    /**
     * User Resource
     *
     * @var Betta\Docusign\Resources\User\UserResource
     */
    public $userResource;


    /**
    * Constructs the internal representation of the Docusign User service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Create the Client
        parent::__construct($client);

        # make proeprty a Resource
        $this->statusResource = new UserResource( $this );
    }
}
