<?php

namespace Betta\Docusign\Resources\Signature;

use Betta\Docusign\Foundation\DocusignModel;

class Recipient extends DocusignModel
{
    /**
     * signature order
     *
     * @var int
     */
    private $routingOrder;


    /**
     * ID of the recipient
     * @var int
     */
    private $id;


    /**
     * Name of the recipient
     *
     * @var string
     */
    private $name;


    /**
     * Email
     *
     * @var string
     */
    private $email;


    /**
     * Internal to Client Recipient ID
     *
     * @var mixed|int|string
     */
    private $clientId;


    /**
     * Recipient Group
     * Agents | Carbon Copies | Certified Deliveries | Editors | In Person Signers | Intermediaries | Signers
     *
     * @var string
     */
    private $type;


    /**
     * Places to sign
     *
     * @var array
     */
    private $tabs;


    /**
     * Class constructor
     *
     * @param int $routingOrder
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $clientId
     * @param string $type
     * @param array $tabs
     */
    public function __construct($routingOrder =null, $id = null, $name = null, $email = null, $clientId = NULL, $type = 'signers', $tabs = NULL)
    {
        if( isset($routingOrder) ) $this->routingOrder = $routingOrder;
        if( isset($id) )           $this->id           = $id;
        if( isset($name) )         $this->name         = $name;
        if( isset($email) )        $this->email        = $email;
        if( isset($type) )         $this->type         = $type;

        # Ensure that a client id only gets assigned to allowed recipient types.
        if (isset($clientId)){
            switch ($type)
            {
                case 'signers'            :
                case 'agents'             :
                case 'intermediaries'     :
                case 'editors'            :
                case 'certifiedDeliveries':
                    $this->clientId = $clientId;
                    break;
            }
        }

        # Injuect formatted Tabs
        if( isset($tabs) && is_array($tabs)){
            foreach ($tabs as $tabType => $tab){
                foreach ($tab as $singleTab){
                    $this->setTab($tabType, $singleTab);
                }
            }
        }
    }


    /**
     * Set the Routing order
     *
     * @param string $routingOrder
     * @return  Recipient
     */
    public function setRoutingOrder($routingOrder)
    {
        # set Value
        $this->routingOrder = $routingOrder;

        # make method chainable
        return $this;
    }

    /**
     * Get Routing order
     *
     * @return int
     */
    public function getRoutingOrder()
    {
        return $this->routingOrder;
    }

    /**
     * Set the Recipient ID
     *
     * @param   string $id
     * @return  Recipient
     */
    public function setId($id)
    {
        # vet value
        $this->id = $id;

        # make method chainable
        return $this;
    }

    /**
     * Return the ID of the Recipient
     *
     * @return Int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set the User Name
     *
     * @param   string $name
     * @return  Recipient
     */
    public function setName($name)
    {
        # set value
        $this->name = $name;

        # make method chainable
        return $this;
    }


    /**
     * Returhn Recipent Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set the User Name
     *
     * @param   string $name
     * @return  Recipient
     */
    public function setEmail($email)
    {
        # set value
        $this->email = $email;

        # make method chainable
        return $this;
    }


    /**
     * Return thr Recipient email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Set Internal ClientID
     *
     * @param   string $clientId
     * @return  Recipient
     */
    public function setClientId($clientId)
    {
        # set value
        $this->clientId = $clientId;

        # make method chainable
        return $this;
    }

    /**
     * Return the ClientId
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set The Recipient Group
     *
     * @param   string $type
     * @return  Recipient
     */
    public function setType($type)
    {
        # Set value
        $this->type = $type;

        # make method chainable
        return $this;
    }

    /**
     * Return thr Recipient's group
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Return signature Tabs
     *
     * @return Array
     */
    public function getTabs()
    {
        return $this->tabs;
    }


    /**
     * Get specific tab
     *
     * @param  string $tabType
     * @param  string $tabLabel
     * @return Array| null
     */
    public function getTab($tabType, $tabLabel)
    {
        foreach ($this->tabs[$tabType] as $tab){
            if ($tab['tabLabel'] == $tabLabel) {
                return array($tabType => $tab);
            }
        }
    }

    /**
     * Add the Signature Tab
     *
     * @param string $tabType
     * @param array $tab
     */
    public function setTab($tabType, $tab)
    {
        # if the value is array, iterate through all
        if( is_array(head($tab)) ){
            foreach($tab as $subTab){
                $this->setTab($tabType, $subTab);
            }
            return $this;
        }

        //.. construct tab array
        switch ($tabType) {
            case 'approveTabs':
            case 'checkboxTabs':
            case 'companyTabs':
            case 'dateSignedTabs':
            case 'dateTabs':
            case 'declineTabs':
            case 'emailTabs':
            case 'emailAddressTabs':
            case 'envelopeIdTabs':
            case 'firstNameTabs':
            case 'formulaTabs':
            case 'fullNameTabs':
            case 'initialHereTabs':
            case 'lastNameTabs':
            case 'noteTabs':
            case 'listTabs':
            case 'numberTabs':
            case 'radioGroupTabs':
            case 'signHereTabs':
            case 'signerAttachmentTabs':
            case 'ssnTabs':
            case 'textTabs':
            case 'titleTabs':
            case 'zipTabs':
                $this->tabs[$tabType][] = $tab;
                break;
        };

        return $this;
    }


    /**
     * Remove the tab
     *
     * @param  string $tabType
     * @param  string $tabLabel
     * @return Void
     */
    public function unsetTab($tabType, $tabLabel)
    {
        foreach ($this->tabs[$tabType] as &$tab) {
            if ($tab['tabLabel'] == $tabLabel) {
                unset($tab);
            }
        }
    }
}
