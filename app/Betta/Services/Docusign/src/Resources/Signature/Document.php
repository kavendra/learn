<?php

namespace Betta\Docusign\Resources\Signature;

use Betta\Docusign\Foundation\DocusignModel;

class Document extends DocusignModel
{
    /**
     * Store file name
     *
     * @var string
     */
    private $name;


    /**
     * File order
     *
     * @var int
     */
    private $id;


    /**
     * Store file contents
     *
     * @var string
     */
    private $content;


    /**
     * Class constructor
     * @param string $name
     * @param int $id
     * @param stirng $content
     */
    public function __construct($name = null , $id = null,  $content = null )
    {
        if( isset($name) )    $this->name       = $name;
        if( isset($id) )      $this->id         = $id;
        if( isset($content) ) $this->content    = $content;
    }


    /**
     * Set the File name
     *
     * @param string $name
     */
    public function setName($name)
    {
        # set value
        $this->name = $name;

        # make method chainable
        return $this;
    }


    /**
     * Return file name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Set the ID
     * @param int $id
     */
    public function setId($id)
    {
        # set value
        $this->id = $id;

        # make method chainable
        return $this;
    }


    /**
     * Retun thr ID of the document
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set file content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        # set value
        $this->content = $content;

        # make method chainable
        return $this;
    }


    /**
     * Obtain the content of the file
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
