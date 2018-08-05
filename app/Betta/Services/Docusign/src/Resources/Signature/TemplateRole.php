<?php

namespace Betta\Docusign\Resources\Signature;

use Betta\Docusign\Foundation\DocusignModel;

class TemplateRole extends DocusignModel
{
    /**
     * Tempalte Role Name
     *
     * @var string
     */
    private $roleName;


    /**
     * Template Name
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
     * Signature locations
     *
     * @var Array
     */
    private $tabs;


    /**
     * Class constructor
     *
     * @param string $roleName
     * @param string $name
     * @param string $email
     * @param array $tabs
     */
    public function __construct($roleName, $name, $email, $tabs = NULL)
    {
        if( isset($roleName) ) $this->roleName  = $roleName;
        if( isset($name) )     $this->name      = $name;
        if( isset($email) )    $this->email     = $email;

        if( isset($tabs) && is_array($tabs))
        {
            foreach ($tabs as $tabType => $tab)
            {
                foreach ($tab as $singleTab)
                {
                    $this->setTab($tabType, $singleTab);
                }
            }
        }
    }


    /**
     * Set email for template
     *
     * @param string $email
     * @return Betta\Docusign\Resources\Signature\TemplateRole
     */
    public function setRoleName( $roleName )
    {
        # set value
        $this->roleName = $roleName;

        # return instance
        return $this;
    }


    /**
     * Get the value of Tempalte Role
     *
     * @return string
     */
    public function getRolename()
    {
        return $this->roleName;
    }


    /**
     * Set email for template
     *
     * @param string $email
     * @return Betta\Docusign\Resources\Signature\TemplateRole
     */
    public function setName($name)
    {
        # set value
        $this->name = $name;

        # return instance
        return $this;
    }


    /**
     * Get the value of Tempalte Role
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set email for template
     *
     * @param string $email
     * @return Betta\Docusign\Resources\Signature\TemplateRole
     */
    public function setEmail($email)
    {
        # set value
        $this->email = $email;

        # return instance
        return $this;
    }


    /**
     * Retrun Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    /**
     * Retrun all tabs
     *
     * @return Array
     */
    public function getTabs()
    {
        return $this->tabs;
    }


    /**
     * Get Signature Tab
     *
     * @param  string  $tabType
     * @param  string  $tabLabel
     * @return Array|null
     */
    public function getTab( $tabType, $tabLabel )
    {
        foreach ($this->tabs[$tabType] as $tab)
        {
            if ($tab['tabLabel'] == $tabLabel)
            {
                return array($tabType => $tab);
            }
        }
    }


    /**
     * Set Signatutre location
     *
     * @param string $tabType
     * @param array $tab
     */
    public function setTab( $tabType, $tab )
    {
        //.. construct tab array
        switch ($tabType) {
            case 'signHereTabs':
            case 'initialHereTabs':
            case 'fullNameTabs':
            case 'emailTabs':
            case 'textTabs':
            case 'titleTabs':
            case 'companyTabs':
                $this->tabs[$tabType][] = $tab;
                break;
        };
    }


    /**
     * Remove Signature tab
     *
     * @param  string $tabType
     * @param  array $tabLabel
     * @return Void
     */
    public function unsetTab($tabType, $tabLabel)
    {
        foreach ($this->tabs[$tabType] as &$tab){
            if ($tab['tabLabel'] == $tabLabel){
                unset($tab);
            }
        }
    }
}
