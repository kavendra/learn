<?php

namespace Betta\Docusign\Resources\Account;

use Betta\Docusign\Foundation\DocusignModel;

class InitialUser extends DocusignModel
{
    /**
     * User Email
     *
     * @var string
     */
    private $email;


    /**
     * First Name
     *
     * @var string
     */
    private $firstName;


    /**
     * Last Name
     *
     * @var string
     */
    private $lastName;


    /**
     * Username
     *
     * @var string
     */
    private $userName;


    /**
     * Password
     *
     * @var string
     */
    private $password;


    /**
     * Class constructor
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $userName
     * @param string $password
     */
    public function __construct($email, $firstName, $lastName, $userName, $password = '')
    {
        if( isset($email) )     $this->email     = $email;
        if( isset($firstName) ) $this->firstName = $firstName;
        if( isset($lastName) )  $this->lastName  = $lastName;
        if( isset($userName) )  $this->userName  = $userName;
        if( isset($password) )  $this->password  = $password;
    }

    /**
     * Set User's Email
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\InitialUser
     */
    public function setEmail( $email )
    {
        # set value
        $this->email = $email;

        # return instance
        return $this;
    }


    /**
     * Get User's Email
     *
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set User's First Name
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\InitialUser
     */
    public function setFirstName( $firstName )
    {
        # set value
        $this->firstName = $firstName;

        # return instance
        return $this;
    }


    /**
     * Get User's First Name
     *
     * @return String
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set User's Last Name
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\InitialUser
     */
    public function setLastName( $lastName )
    {
        # Set value
        $this->lastName = $lastName;

        # return instance
        return $this;
    }


    /**
     * Get User's Last Name
     *
     * @return String
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Set User's Username
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\InitialUser
     */
    public function setUserName( $userName )
    {
        # Set value
        $this->userName = $userName;

        # return instance
        return $this;
    }


    /**
     * Get User's Username
     *
     * @return String
     */
    public function getUserName()
    {
        return $this->userName;
    }


    /**
     * Set User's Password
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\InitialUser
     */
    public function setPassword( $password )
    {
        # set Value
        $this->password = $password;

        # return instance
        return $this;
    }


    /**
     * Get User's Password
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }
}
