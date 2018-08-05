<?php

namespace Betta\Docusign\Resources\User;

use Betta\Docusign\DocusignClient;
use Betta\Docusign\Foundation\DocusignService;
use Betta\Docusign\Foundation\DocusignResource;

class UserResource extends DocusignResource
{

    /**
     * Class constructor
     *
     * @param DocusignService $service
     */
    public function __construct(DocusignService $service)
    {
        # Injuect Service
        parent::__construct( $service );
    }

    /**
     * Get Profile Permissions
     *
     * @return Resposne
     */
    public function getPermissionProfileList()
    {
        $url = $this->client->getBaseURL() . '/permission_profiles';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }

    /**
     * Get Groups
     *
     * @return Response
     */
    public function getGroupList()
    {
        $url = $this->client->getBaseURL() . '/groups';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders());
    }

    /**
     * Get User List
     *
     * @param  string $additional_info
     * @return Response
     */
    public function getUserList($additional_info = "false")
    {
        $url = $this->client->getBaseURL() . '/users';
        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders(), array("additional_info" => $additional_info));
    }

    /**
     * Get User Info
     *
     * @param  string $user_id
     * @param  string $additional_info
     * @return Response
     */
    public function getUserInfo($user_id, $additional_info = "false")
    {
        $url = $this->client
                                ->getBaseURL() . '/users/' . $user_id;

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders(), array("additional_info" => $additional_info));
    }

    /**
     * Get User List Settings
     *
     * @param  string $user_id
     * @param  string $additional_info
     * @return Response
     */
    public function getUserSettingList($user_id, $additional_info = "false")
    {
        $url = $this->client->getBaseURL() . '/users/' . $user_id . '/settings';

        return $this->curl->makeRequest($url, 'GET', $this->client->getHeaders(), array("additional_info" => $additional_info));
    }

    /**
     * Add new User
     *
     * @param Betta\Docusign\Resources\User\AddUser $addUser
     */
    public function addUser($addUser)
    {
        $url = $this->client->getBaseURL() . '/users';

        $data["newUsers"] = array( $addUser->getData() );

        return $this->curl->makeRequest($url, 'POST', $this->client->getHeaders(), array(), json_encode($data));
    }

    /**
     * Finilize user
     *
     * @param  string $user_id
     * @return Response
     */
    public function closeUser($user_id)
    {
        $url = $this->client->getBaseURL() . '/users';

        $data["users"] = array(array("userId" => $user_id));

        return $this->curl->makeRequest($url, 'DELETE', $this->client->getHeaders(), array(), json_encode($data));
    }

    /**
     * Get User Profile
     *
     * @param  string $user_id
     * @return Response
     */
    public function getUserProfile($user_id)
    {
        // To view a user profile, the SendOnBehalfOf (SOBO) functionality must also be used. To use this
        // functionality, we require the user email address that matches the supplied userid. For this
        // implementation, we use the getUserInfo method to look up that email address.
        $user_info       = $this->getUserInfo($user_id);
        $sobo_user_email = $user_info->email;

        $url = $this->client->getBaseURL() . '/users/' . $user_id . '/profile';

        // As part of the SendOnBehalfOf functionality, we must supply the user email address in the header.
        return $this->curl->makeRequest($url, 'GET', $this->client->getSoboHeaders($sobo_user_email));
    }

    /**
     * Apply changes to User PRofile
     *
     * @param  string $user_id
     * @param  Betta\Docusign\Resources\User\UserProfile $userProfile
     * @return [type]               [description]
     */
    public function modifyUserProfile($user_id, $userProfile)
    {
        // To modify a user profile, the SendOnBehalfOf (SOBO) functionality must also be used. To use this
        // functionality, we require the user email address that matches the supplied userid. For this
        // implementation, we use the getUserInfo method to look up that email address.
        $user_info       = $this->getUserInfo($user_id);
        $sobo_user_email = $user_info->email;

        $url = $this->client->getBaseURL() . '/users/' . $user_id . '/profile';

        // As part of the SendOnBehalfOf functionality, we must supply the user email address in the header.
        return $this->curl->makeRequest($url, 'PUT', $this->client->getSoboHeaders($sobo_user_email), array(), json_encode($userProfile->getData()));
    }
}
