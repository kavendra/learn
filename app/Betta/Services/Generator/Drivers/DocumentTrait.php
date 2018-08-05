<?php

namespace Betta\Services\Generator\Drivers;

use File;
use Betta\Models\Document;

trait DocumentTrait
{

    /**
     * Define MD5 hash match
     *
     * @var string
     */
    protected $__MD5_MATCH = '/^[a-f0-9]{32}/';

    /**
     * Convert the file to Document
     *
     * @param  boolean $persist
     * @return Document
     */
    public function toDocument($persist = false)
    {
        $document = new Document([
            'label' => $this->getDocumentLabel(),
            'description' => $this->getDocumentDescription(),
            'original_name' => $this->getDocumentOriginalName(),
            'file_name' => $this->getDocumentFileName(),
            'mime_type' => $this->getDocumentMimeType(),
            'size' => $this->getDocumentSize(),
            'uri' => $this->getDocumentUri(),
        ]);

        if ($persist===true){
            $document->save();
        }

        return $document;
    }

    /**
     * Create Document Label
     *
     * @return string
     */
    protected function getDocumentLabel()
    {
        $name = File::basename( $this->path );

        $name = trim(preg_replace($this->__MD5_MATCH, '', $name));

        return ltrim($name, '- ');
    }

    /**
     * Create Document Label
     *
     * @return string
     */
    protected function getDocumentDescription()
    {
        return '';
    }


    /**
     * Create Document Label
     *
     * @return string
     */
    protected function getDocumentOriginalName()
    {
        return File::basename( $this->path );
    }


    /**
     * Create Document Label
     *
     * @return string
     */
    protected function getDocumentFileName()
    {
        return File::basename( $this->path );
    }


    /**
     * Create Document Label
     *
     * @return string
     */
    protected function getDocumentMimeType()
    {
        return File::mimeType( $this->path );
    }


    /**
     * Filesize of the Document
     *
     * @return string
     */
    protected function getDocumentSize()
    {
        return File::size( $this->path );
    }


    /**
     * What is the URI?
     *
     * @return string
     */
    protected function getDocumentUri()
    {
        return $this->path;
    }
}
