<?php

namespace Betta\Services\Generator\Drivers;

use Illuminate\Filesystem\Filesystem;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Contracts\Support\Arrayable;
use Betta\Services\Generator\Interfaces\TemplateInterface;

class WordTemplate implements TemplateInterface, Arrayable
{
    /**
     * Result could be converted to Document
     */
    use DocumentTrait;

    /**
     * Result could be converted to array
     */
    use ArrayableTemplate;

    /**
     * Where shall we store conversion results?
     *
     * @var string
     */
    protected $storage_path;

    /**
     * Share the Tempalte
     *
     * @var TemplateProcessor
     */
    protected $template;

    /**
     * Template Processor, implementation of
     *
     * @var \PhpOffice\PhpWord\TemplateProcessor
     */
    protected $processor = TemplateProcessor::class;

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
     * @var string
     */
    public $stream;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        # inject tempalte processor
        $this->template     = new $this->processor($path);
        $this->filesystem   = new Filesystem;

        # We can later offload to anywhere else;
        $this->storage_path = storage_path('app/temp');
    }

    /**
     * Return thr phpWord instance at Path
     *
     * @return PhpWord
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Merge the Word Document Template as Path and Save it to saveAs
     *
     * @param  string $path
     * @param  array  $data
     * @param  string $saveAs
     * @return Instsance
     */
    public function merge($data = array(), $offset = null)
    {
        foreach (array_change_key_case($data, CASE_UPPER) as $mergeField => $value) {
            # if the value can be converted to array, Clone the row in the Template
            if (is_array($value))
            {
                # Clone the row
                $this->template->cloneRow($mergeField, count($value));
                # and now fill the cloned
                foreach($value as $offset => $item ){
                    $this->merge( $item, $offset+1 );
                }
            } else{
                $this->template->setValue( $this->transformFieldName($mergeField, $offset), $this->transformValue($value));
            }
        }

        return $this;
    }

    /**
     * Save the template somewhere
     * @param  string $saveAs
     * @return boolean?
     */
    public function save($saveAs)
    {
        $this->template->saveAs($saveAs);

        $this->finalize($saveAs);

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
     * Transform the Field Name
     *
     * @param  string   $fieldName
     * @param  null|int $offset
     * @param  string   $glue
     * @return string
     */
    protected function transformFieldName($fieldName, $offset = null, $glue = '#')
    {
        return  empty($offset) ? $fieldName : implode($glue, [$fieldName, $offset]);
    }

    /**
     * Transform the value to accout for line breaks
     *
     * @param  string $value
     * @return string
     */
    private function transformValue($value = '')
    {
        # Replace Ampersands, quotes etc
        $value = htmlspecialchars($value);

        # Replace line breaks
        $value = preg_replace('~\R~u', '</w:t><w:br/><w:t>', $value);

        # return parsed value
        return $value;
    }

    /**
     * Try to convert the file to PDF
     *
     * @return Instance
     */
    public function convertToPdf($replace = true, $storagePath = null)
    {

        # Reset storage Path
        if(!empty($storagePath)){
            $this->storage_path = $storagePath;
        }

        # $output will contain the response from exec
        exec( $this->getConvertCommand($this->path), $output);

        # Name of the converted file
        $convertedPath = sprintf('%s/%s.pdf', $this->storage_path, $this->filesystem->name($this->path));

        # Replace the values in generated source
        if ($replace === true and $this->filesystem->exists($convertedPath)) {
            $this->finalize($convertedPath);
        }

        # return Instance
        return $this;
    }



    /**
     * Produce convert Command
     *
     * @param  string $filename
     * @return string
     */
    protected function getConvertCommand($filename)
    {
        # Command to use
        $command = "soffice --headless --convert-to pdf '%s' --outdir '%s' -env:UserInstallation=file:///tmp";

        return sprintf($command, realpath($filename), realpath($this->storage_path) );
    }
}
