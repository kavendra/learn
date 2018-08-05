<?php

namespace Betta\Docusign\Resources\Account;

use Betta\Docusign\Foundation\DocusignModel;

class ReferralInformation extends DocusignModel
{
    /**
     * Referral Code
     *
     * @var string
     */
    private $referralCode;

    /**
     * Rererer's Name
     *
     * @var string
     */
    private $referrerName;


    /**
     * Class constructor
     *
     * @param string $referralCode
     * @param string $referrerName
     */
    public function __construct($referralCode, $referrerName)
    {
        if( isset($referralCode) ) $this->referralCode = $referralCode;
        if( isset($referrerName) ) $this->referrerName = $referrerName;
    }

    /**
     * Set Referral Code
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\ReferralInformation
     */
    public function setReferralCode( $referralCode )
    {
        # set value
        $this->referralCode = $referralCode;

        # return instance
        return $this;
    }


    /**
     * Get Referral Code
     *
     * @param String
     */
    public function getReferralCode()
    {
        return $this->referralCode;
    }

    /**
     * Set Referer's Name
     *
     * @param String
     * @return Betta\Docusign\Resources\Account\ReferralInformation
     */
    public function setReferrerName( $referrerName )
    {
        # set value
        $this->referrerName = $referrerName;

        # return instance
        return $this;
    }

    /**
     * Get Referer's Name
     *
     * @param String
     */
    public function getReferrerName()
    {
        return $this->referrerName;
    }
}
