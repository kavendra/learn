<?php

namespace Betta\Docusign;

use Betta\Docusign\Io\CurlIo;
use Betta\Docusign\Io\Credentials;
use Betta\Docusign\Exceptions\IoException;

class DocusignClient
{

    /**
     * Contain the Credentials
     *
     * @var Betta\Docusign\Io\Credentials
     */
    public $creds;


    /**
     * The version of DocuSign API
     *
     * @var string
     */
    public $version;


    /**
     * The DocuSign Environment
     *
     * @var string
     */
    public $environment;


    /**
     * The base url of the DocuSign Account
     *
     * @var string
     */
    public $baseURL;


    /**
     * The DocuSign Account Id
     *
     * @var string
     */
    public $accountID;


    /**
     * Docusign CurlIO
     *
     * @var Betta\Docusign\Io\CurlIo
     */
    public $curl;


    /**
     * The flag indicating if it has multiple DocuSign accounts
     *
     * @var boolean
     */
    public $hasMultipleAccounts = false;


    /**
     * True if has errors
     *
     * @var boolean
     */
    public $hasError = false;


    /**
     * Last error message
     *
     * @var string
     */
    public $errorMessage = '';


    /**
     * Class constructor
     *
     * @param array|null $config
     */
    public function __construct( $config = null )
    {
        # load the Credentials from the $config
        $this->version     = array_get($config, 'version', 'v2');
        $this->environment = array_get($config, 'environment', 'demo');
        $this->accountID   = array_get($config, 'account_id', null);

        $this->creds       = new Credentials( $config['integrator_key'], $config['email'], $config['password'] );
        $this->curl        = new CurlIo();

        if( $this->creds->isEmpty() ){
            $this->hasError     = true;
            $this->errorMessage = "One or more missing config settings found.  Please check config.php, or pass in required credentials to DocusignClient class constructor.";
        } else  {
            $this->authenticate();
        }
    }


    /**
     * Authenticate against Docusign
     *
     * @return Void
     */
    public function authenticate()
    {
        # Get the URL
        $url = $this->getLoginUrl();

        try {
            $response = $this->curl->get($url,  $this->getHeaders() );
        } catch ( IoException $e) {
            $this->hasError     = true;
            $this->errorMessage = $e->getMessage();

            return;
        }

        # @todo
        #
        # split functionality
        #
        if( count($response->loginAccounts) > 1 )
        {
            $this->hasMultipleAccounts = true;
        }

        $defaultBaseURL   = '';
        $defaultAccountID = '';

        foreach($response->loginAccounts as $account) {
            # fetch the first account that matched the current' user's account
            if( !empty($this->accountID) ) {
                if( $this->accountID == $account->accountId ) {
                    $this->baseURL = $account->baseUrl;
                    break;
                }
            }

            if( $account->isDefault == 'true' ) {
                $defaultBaseURL   = $account->baseUrl;
                $defaultAccountID = $account->accountId;
            }
        }

        if( empty($this->baseURL) ) {
            $this->baseURL   = $defaultBaseURL;
            $this->accountID = $defaultAccountID;
        }

        return $response;
    }


    /**
     * Return Credentials
     *
     * @return String
     */
    public function getCreds()
    {
        return $this->creds;
    }


    /**
     * Return Environment valur
     *
     * @return String
     */
    public function getVersion()
    {
        return $this->version;
    }


    /**
     * Return Current Environment
     *
     * @return String
     */
    public function getEnvironment()
    {
        return $this->environment;
    }


    /**
     * Return Base URL
     *
     * @return String
     */
    public function getBaseURL()
    {
        return $this->baseURL;
    }


    /**
     * Return the Base Url with whatevenr needs to be appended
     *
     * @return string
     */
    public function makeBaseUrl( $appends = null )
    {
        return implode('/', array_merge([ $this->baseURL ], (array) $appends)  );
    }


    /**
     * Return the login URL
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return "https://{$this->environment}.docusign.net/restapi/{$this->version}/login_information";
    }


    /**
     * Return current Account ID
     *
     * @return string
     */
    public function getAccountID()
    {
        return $this->accountID;
    }


    /**
     * Return current Curl
     *
     * @return Betta\Docusign\Io\CurlIo
     */
    public function getCUrl()
    {
        return $this->curl;
    }


    /**
     * True if user has multiple accounts
     *
     * @return String
     */
    public function hasMultipleAccounts()
    {
        return $this->hasMultipleAccounts;
    }


    /**
     * True if class has error
     *
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }


    /**
     * Get the Error message
     *
     * @return String
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


    /**
     * Get Standard Authentication Headers
     *
     * @param  string $accept
     * @param  string $contentType
     * @return string
     */
    public function getHeaders($accept = 'Accept: application/json', $contentType = 'Content-Type: application/json')
    {
        return array(
            'X-DocuSign-Authentication: <DocuSignCredentials><Username>' . $this->creds->getEmail() . '</Username><Password>' . $this->creds->getPassword() . '</Password><IntegratorKey>' . $this->creds->getIntegratorKey() . '</IntegratorKey></DocuSignCredentials>',
            $accept,
            $contentType
        );
    }


    /**
     * Signed on Behalf-headers
     *
     * @param  string $soboUser
     * @param  string $accept
     * @param  string $contentType
     * @return string
     */
    public function getSoboHeaders($soboUser, $accept = 'Accept: application/json', $contentType = 'Content-Type: application/json')
    {
        return array(
            'X-DocuSign-Authentication: <DocuSignCredentials><SendOnBehalfOf>' . $soboUser . '</SendOnBehalfOf><Username>' . $this->creds->getEmail() . '</Username><Password>' . $this->creds->getPassword() . '</Password><IntegratorKey>' . $this->creds->getIntegratorKey() . '</IntegratorKey></DocuSignCredentials>',
            $accept,
            $contentType
        );
    }
}
