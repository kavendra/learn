<?php

namespace Betta\Services\Generator\Drivers;

use mikehaertl\pdftk\Pdf;
use Illuminate\Filesystem\Filesystem;
use Betta\Services\Generator\Interfaces\TemplateInterface;

class PdfTemplate implements TemplateInterface
{
    /**
     * Result could be converted to Document
     */
    use DocumentTrait;

    /**
     * Where shall we store conversion results?
     *
     * @var string
     */
    protected $storage_path;

    /**
     * Bind the instance
     *
     * @var mikehaertl\pdftk\Pdf;
     */
    private $template;

    /**
     * Resulting Path
     *
     * @var string
     */
    public $path;

    /**
     * Nice Name of the file
     *
     * @var string
     */
    public $file;

    /**
     * File stream
     *
     * @var Filesystem
     */
    public $stream;

    /**
     * Construct the Temaplte
     *
     * @param string $path
     */
    public function __construct($path)
    {
        # Make Template processor
        $this->template     = new Pdf( $path );
        $this->filesystem   = new Filesystem;

        # We can later offload it to anywhere else;
        $this->storage_path = storage_path('temp_pdf');
    }

    /**
     * Return Template instance
     *
     * @return mikehaertl\pdftk\Pdf
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Merge the PDF Template as Path and Save it to saveAs
     *
     * @param  array  $data
     * @return Instance
     */
    public function merge($data = array(), $flatten = true, $transform = false)
    {
        # merge
        $this->getTemplate()->fillForm( $this->transformKeys($data, $transform) )->needAppearances();

        # flatten by default
        if ($flatten===true){
            $this->getTemplate()->flatten();
        }

        return $this;
    }

    /**
     * Save the Template... at provided path URI
     *
     * @param  string $saveAs
     * @return Instance
     */
    public function save($saveAs)
    {
        # save Template
        $this->getTemplate()->saveAs($saveAs);

        # Set paths
        $this->finalize($saveAs);

        # return Isntance
        return $this;
    }

    /**
     * Finalize the properties
     *
     * @param  string $saveAs
     * @return Void
     */
    protected function finalize($saveAs)
    {
        # assign as properties
        $this->path   = $saveAs;

        # Nice Name of the file
        $this->file   = $this->getDocumentLabel();

        # Steam
        $this->stream = $this->filesystem->get($saveAs);
    }

    /**
     * Transform Merge Fields
     * Flattenened using .dot notation: https://laravel.com/docs/5.2/helpers#method-array-dot
     *
     * @param  array  $array
     * @return array
     */
    protected function transformKeys( $array = array(), $transform = true)
    {
        # if we need to transform the keys
        if ( $transform===true ) {
            return array_dot(array_change_key_case($array, CASE_LOWER));
        }

        return $array;
    }

    /**
     * Helper function to return the available merge fields
     *
     * @return array
     * @author CNK
     */
    public function fields()
    {
        return $this->getTemplate()->getDataFields();
    }

    /**
     * Easiest implementation of all: Convert PDF to PDF... returns current instance
     *
     * @return Instance
     */
    public function convertToPdf($replace = true)
    {
        return $this;
    }
}
