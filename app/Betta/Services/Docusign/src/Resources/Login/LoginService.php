<?php

namespace Betta\Docusign\Resources\Login;

use Betta\Docusign\Foundation\DocusignService;

class LoginService extends DocusignService
{

    /**
     * Bind the implementation
     *
     * @var Betta\Docusign\LoginResource
     */
    public $login;


    /**
    * Constructs the internal representation of the Docusign Login service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Construct the Service
        parent::__construct( $client );

        # return representation of Resource
        $this->login = new LoginResource( $this );
    }
}
