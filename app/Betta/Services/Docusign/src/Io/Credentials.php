<?php

namespace Betta\Docusign\Io;

class Credentials
{

    /**
     * The Docusign Integrator's Key
     *
     * @var string
     */
    private $integratorKey;


    /**
     * The Docusign Account Email
     *
     * @var string
     */
    private $email;


    /**
     * The Docusign Account password or API password
     *
     * @var string
     */
    private $password;


    /**
     * Construct Credentials
     *
     * @param string $integratorKey
     * @param string $email
     * @param string $password
     */
    public function __construct($integratorKey, $email, $password)
    {
        $this->integratorKey = $integratorKey;
        $this->email         = $email;
        $this->password      = $password;
    }


    /**
     * Set Integrator  Key
     *
     * @param string $integratorKey
     * @return Betta\Docusign\Io\Credentials
     */
    public function setIntegratorKey( $integratorKey )
    {
        # set value
        $this->integratorKey = $integratorKey;

        # return instance
        return $this;
    }


    /**
     * Get Integrator Key
     *
     * @return string
     */
    public function getIntegratorKey()
    {
        return $this->integratorKey;
    }


    /**
     * Set Email
     *
     * @param string $email
     * @return Betta\Docusign\Io\Credentials
     */
    public function setEmail( $email )
    {
        $this->email = $email;

        # return instance
        return $this;
    }


    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set Password
     *
     * @param string $password
     * @return Betta\Docusign\Io\Credentials
     */
    public function setPassword( $password )
    {
        # set value
        $this->password = $password;

        # return instance
        return $this;
    }


    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * True if the Credentials are incomplete
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return (empty($this->integratorKey) || empty($this->email) || empty($this->password));
    }
}
