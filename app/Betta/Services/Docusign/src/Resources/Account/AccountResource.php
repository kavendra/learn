<?php

namespace Betta\Docusign\Resources\Account;

use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;
use Betta\Docusign\Resources\Account\AccountResource;

class AccountResource extends DocusignResource
{

    /**
     * Class constructor
     *
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        # Inject Service
        parent::__construct( $service );
    }


    /**
     * Provision account
     *
     * @param  string $appToken
     * @return Response
     */
    public function getAccountProvisioning($appToken)
    {
        $url = $this->client->getBaseURL() . '/accounts/provisioning';

        $headers = $this->client->getHeaders();

        return $this->curl->makeRequest($url, 'GET', array_push($headers, 'X-DocuSign-AppToken:' . $appToken) );
    }


    /**
     * Get Account Information
     *
     * @return Response
     */
    public function getInfo()
    {
        $url = $this->client->getBaseURL();

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Bet Billing Plan
     *
     * @return Response
     */
    public function getBillingPlan()
    {
        $url = $this->client->getBaseURL() . '/billing_plan';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get Billing chanrge list
     *
     * @return Response
     */
    public function getBillingChargeList()
    {
        $url = $this->client->getBaseURL() . '/billing_charges';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get the invoice list
     *
     * @return Response
     */
    public function getBillingInvoiceList()
    {
        $url = $this->client->getBaseURL() . '/billing_invoices';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get the Settings
     *
     * @return Response
     */
    public function getSettingList()
    {
        $url = $this->client->getBaseURL() . '/settings';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get the brand list
     *
     * @return Response
     */
    public function getBrandList()
    {
        $url = $this->client->getBaseURL() . '/brands';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get the custom Fields list
     *
     * @return Response
     */
    public function getCustomFieldList()
    {
        $url = $this->client->getBaseURL() . '/custom_fields';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }


    /**
     * Create Account
     *
     * @param  String $accountName
     * @param  string $distributorCode
     * @param  string $distributorPassword
     * @param  array  $planId
     * @param  [array $initialUser
     * @param  array $referralInformation
     * @return Response
     */
    public function createAccount($accountName,
                                  $distributorCode,
                                  $distributorPassword,
                                  $planId,
                                  $initialUser,
                                  $referralInformation ) {

        $url = 'https://' . $this->client->getEnvironment() . '.docusign.net/restapi/v2/accounts';

        $data = array(
            "accountName"         => $accountName,
            "distributorCode"     => $distributorCode,
            "distributorPassword" => $distributorPassword,
            "planInformation"     => array("planId" => $planId),
            "initialUser" => array(
                "email"     => $initialUser->getEmail(),
                "firstName" => $initialUser->getFirstName(),
                "lastName"  => $initialUser->getLastName(),
                "userName"  => $initialUser->getUserName(),
                "password"  => $initialUser->getPassword(),
            ),
            "referralInformation" => array(
                "referralCode" => $referralInformation->getReferralCode(),
                "referrerName" => $referralInformation->getReferrerName(),
            ),
        );

        return $this->curl->makeRequest($url, 'POST', $this->client->getHeaders(), array(), json_encode($data));
    }
}
