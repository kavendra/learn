<?php

namespace Betta\Docusign\Resources\Login;

use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;

class LoginResource extends DocusignResource
{

    /**
     * URL of the API
     *
     * @var string
     */
    protected $url;


    /**
     * Class constructor
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        parent::__construct( $service );

        # Build the URL
        $this->url = $this->buildUrl('login_information');
    }


    /**
     * Get Login information
     *
     * @return Response
     */
    public function getLoginInformation()
    {
        return $this->curl->makeRequest($this->url, 'GET', $this->client->getHeaders());
    }


    /**
     * Get Token
     *
     * @return Response
     */
    public function getToken()
    {
        $this->url = $this->buildUrl('oauth2/token');

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );

        $data = array (
            'grant_type' => 'password',
            'scope'      => 'api',
            'client_id'  => $this->client->getCreds()->getIntegratorKey(),
            'username'   => $this->client->getCreds()->getEmail(),
            'password'   => $this->client->getCreds()->getPassword()
        );

        return $this->curl->makeRequest($this->url, 'POST', $headers, array(), http_build_query($data));
    }


    /**
     * Get the SOBO TOken
     *
     * @param  string $userName user
     * @param  string $bearer    SOBO user
     * @return Response
     */
    public function getTokenOnBehalfOf($userName, $bearer)
    {
        $this->url = $this->buildUrl('oauth2/token');

        $headers = array(
            'Authorization: bearer ' . $bearer,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );

        $data = array(
            'grant_type' => 'password',
            'scope'      => 'api',
            'client_id'  => $this->client->getCreds()->getIntegratorKey(),
            'username'   => $userName,
            'password'   => 'password'
        );

        return $this->curl->makeRequest($this->url, 'POST', $headers, array(), http_build_query($data));
    }


    /**
     * Remove Token
     *
     * @param  string $token
     * @return Response
     */
    public function revokeToken($token)
    {
        $this->url = $this->buildUrl('oauth2/revoke');

        $headers = array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        );

        $data = array(
            'token' => $token
        );

        return $this->curl->makeRequest($this->url, 'POST', $headers, array(), http_build_query($data));
    }


    /**
     * Update User password
     *
     * @param  string $newPassword
     * @return Response
     */
    public function updatePassword($newPassword)
    {
        $this->url = $this->buildUrl('login_information/password');

        $data = array(
            'currentPassword' => $this->client->getCreds()->getPassword(),
            'email'           => $this->client->getCreds()->getEmail(),
            'newPassword'     => $newPassword
        );

        return $this->curl->makeRequest($this->url, 'PUT', $this->client->getHeaders(), array(), json_encode($data));
    }


    /**
     * Build URL
     *
     * @param  string $append
     * @return string
     */
    private function buildUrl( $append = '')
    {
        return 'https://' . $this->client->getEnvironment() . '.docusign.net/restapi/' . $this->client->getVersion() .'/'. $append;
    }
}
