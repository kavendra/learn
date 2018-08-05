<?php

namespace Betta\Docusign\Resources\User;

use Betta\Docusign\Foundation\DocusignModel;

/**
* This class encapsulates the possible parameters that can be supplied when modifying a
* user profile.
*/
class UserProfile extends DocusignModel
{
    /**
     * The internal representation of the data is in the form
     * that can be directly used in the API call to modify a user profile.
     * @var array
     */
    private $data = array();

    /**
     * Class constructor
     *
     * @param array $data
     */
    public function __construct($data = array())
    {
        if( isset($data) ) $this->data = $data;
    }

    /**
     * Set Data
     *
     * @param $data
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setData($data)
    {
        # set value
        $this->data = $data;

        # return instance
        return $this;
    }


    /**
     * Get the Data
     *
     * @return Array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Save Address Line 1
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setAddress1($value)
    {
        # set Value
        array_set($this->data, 'address.address1', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Address Line 2
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setAddress2($value)
    {
        # set Value
        array_set($this->data, 'address.address2', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save City
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setCity($value)
    {
        # set Value
        array_set($this->data, 'address.city', $value);

        # retirn instance
        return $this;
    }

    /**
     * Save Address Country
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setCountry($value)
    {
        # set Value
        array_set($this->data, 'address.country', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Address Fax
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setFax($value)
    {
        # set Value
        array_set($this->data, 'address.fax', $value);

        # retirn instance
        return $this;
    }

    /**
     * Save Address Phone
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setPhone($value)
    {
        # set Value
        array_set($this->data, 'address.phone', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Address Postal Code / ZIP
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setPostalCode($value)
    {
        # set Value
        array_set($this->data, 'address.postalCode', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Address Zip
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setZip($value)
    {
        return $this->setPostalCode($value)
    }


    /**
     * Save Address StateOrProvince
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setStateOrProvince($value)
    {
        # set Value
        array_set($this->data, 'address.stateOrProvince', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Address State
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setState($value)
    {
        return $this->setStateOrProvince($value);
    }


    /**
     * Save Company Name
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setCompanyName($value)
    {
        # set Value
        array_set($this->data, 'companyName', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Company Display Name
     *
     * @param boolean $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setDisplayOrganizationInfo($value)
    {
        # set Value
        array_set($this->data, 'displayOrganizationInfo', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Display Personal Information
     *
     * @param boolean $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setDisplayPersonalInfo($value)
    {
        # set Value
        array_set($this->data, 'displayPersonalInfo', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Display Profile
     *
     * @param boolean $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setDisplayProfile($value)
    {
        # set Value
        array_set($this->data, 'displayProfile', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Display Profile History
     *
     * @param boolean $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setDisplayUsageHistory($value)
    {
        # set Value
        array_set($this->data, 'displayUsageHistory', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save Title Information
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setTitleInfo($value)
    {
        # set Value
        array_set($this->data, 'title', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save User's First Name
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setFirstName($value)
    {
        # set Value
        array_set($this->data, 'userDetails.firstName', $value);

        # retirn instance
        return $this;
    }

    /**
     * Save User's Middle Name
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setMiddleName($value)
    {
        # set Value
        array_set($this->data, 'userDetails.middleName', $value);

        # retirn instance
        return $this;
    }

    /**
     * Save User's Last Name
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setLastName($value)
    {
        # set Value
        array_set($this->data, 'userDetails.lastName', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save User's Suffix
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setSuffixName($value)
    {
        # set Value
        array_set($this->data, 'userDetails.suffixName', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save User's Title
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setTitle($value)
    {
        # set Value
        array_set($this->data, 'userDetails.title', $value);

        # retirn instance
        return $this;
    }


    /**
     * Save User's Username
     *
     * @param string $value
     * @return Betta\Docusign\Resources\User\UserProfile
     */
    public function setUserName($value)
    {
        # set Value
        array_set($this->data, 'userDetails.userName', $value);

        # retirn instance
        return $this;
    }


        /**
     * Get Address Line 1
     *
     * @return string
     */
    public function getAddress1($default='')
    {
        return array_get($this->data, 'address.address1', $default);
    }


    /**
     * Get Address Line 2
     *
     * @return string
     */
    public function getAddress2($default='')
    {
        return array_get($this->data, 'address.address2', $default);
    }


    /**
     * Get City
     *
     * @return string
     */
    public function getCity($default='')
    {
        return array_get($this->data, 'address.city', $default);
    }

    /**
     * Get Address Country
     *
     * @return string
     */
    public function getCountry($default='')
    {
        return array_get($this->data, 'address.country', $default);
    }


    /**
     * Get Address Fax
     *
     * @return string
     */
    public function getFax($default='')
    {
        return array_get($this->data, 'address.fax', $default);
    }

    /**
     * Get Address Phone
     *
     * @return string
     */
    public function getPhone($default='')
    {
        return array_get($this->data, 'address.phone', $default);
    }


    /**
     * Get Address Postal Code / ZIP
     *
     * @return string
     */
    public function getPostalCode($default='')
    {
        return array_get($this->data, 'address.postalCode', $default);
    }


    /**
     * Get Address Zip
     *
     * @return string
     */
    public function getZip($default='')
    {
        return $this->getPostalCode($default)
    }


    /**
     * Get Address StateOrProvince
     *
     * @return string
     */
    public function getStateOrProvince($default='')
    {
        return array_get($this->data, 'address.stateOrProvince', $default);
    }


    /**
     * Get Address State
     *
     * @return string
     */
    public function getState($default='')
    {
        return $this->getStateOrProvince($default);
    }


    /**
     * Get Company Name
     *
     * @return string
     */
    public function getCompanyName($default='')
    {
        return array_get($this->data, 'companyName', $default);
    }


    /**
     * Get Company Display Name
     *
     * @return string
     */
    public function getDisplayOrganizationInfo($default='')
    {
        return array_get($this->data, 'displayOrganizationInfo', $default);
    }


    /**
     * Get Display Personal Information
     *
     * @return string
     */
    public function getDisplayPersonalInfo($default='')
    {
        return array_get($this->data, 'displayPersonalInfo', $default);
    }


    /**
     * Get Display Profile
     *
     * @return string
     */
    public function getDisplayProfile($default='')
    {
        return array_get($this->data, 'displayProfile', $default);
    }


    /**
     * Get Display Profile History
     *
     * @return string
     */
    public function getDisplayUsageHistory($default='')
    {
        return array_get($this->data, 'displayUsageHistory', $default);
    }


    /**
     * Get Title Information
     *
     * @return string
     */
    public function getTitleInfo($default='')
    {
        return array_get($this->data, 'title', $default);
    }


    /**
     * Get User's First Name
     *
     * @return string
     */
    public function getFirstName($default='')
    {
        return array_get($this->data, 'userDetails.firstName', $default);
    }

    /**
     * Get User's Middle Name
     *
     * @return string
     */
    public function getMiddleName($default='')
    {
        return array_get($this->data, 'userDetails.middleName', $default);
    }

    /**
     * Get User's Last Name
     *
     * @return string
     */
    public function getLastName($default='')
    {
        return array_get($this->data, 'userDetails.lastName', $default);
    }


    /**
     * Get User's Suffix
     *
     * @return string
     */
    public function getSuffixName($default='')
    {
        return array_get($this->data, 'userDetails.suffixName', $default);
    }


    /**
     * Get User's Title
     *
     * @return string
     */
    public function getTitle($default='')
    {
        return array_get($this->data, 'userDetails.title', $default);
    }


    /**
     * Get User's Username
     *
     * @return string
     */
    public function getUserName($default='')
    {
        return array_get($this->data, 'userDetails.userName', $default);
    }
}
