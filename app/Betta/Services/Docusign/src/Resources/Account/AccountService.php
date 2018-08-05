<?php

namespace Betta\Docusign\Resources\Account;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Resources\Account\AccountResource;

class AccountService extends DocusignService
{

    public $account;

    /**
    * Constructs the internal representation of the Docusign Account service.
    *
    * @param DocusignClient $client
    */
    public function __construct(DocusignClient $client)
    {
        # Construct the Service
        parent::__construct( $client );

        # return representation of Resource
        $this->account = new AccountResource( $this );
    }

    /**
     * Get Information
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->account->getInfo();
    }
}
