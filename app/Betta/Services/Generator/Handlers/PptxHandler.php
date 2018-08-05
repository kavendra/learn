<?php

namespace Betta\Services\Generator\Handlers;

use File;
use ZipArchive;
use Betta\Models\Document;

class PptxHandler
{
    /**
     * Zip file
     *
     * @var ZipArchive
     */
    protected $zipArchive;

    /**
     * Original Path
     *
     * @var string
     */
    protected $document;

    /**
     * Temporary Path
     *
     * @var string
     */
    protected $tempDocumentFilename;

    /**
     * Temporary Path
     *
     * @var string
     */
    protected $newDocumentFilename;

    /**
     * List the slides
     *
     * @var array
     */
    protected $slides;

    /**
     * Variable RegExp
     *
     * @var string
     */
    protected $regexp ='/\$\{(.*?)}/i';

    /**
     * Create new instance of the Handler
     */
    public function __construct(Document $document)
    {
        # Clone the Document
        $this->document = $document->replicate();
        # set the class for the ZipArchive
        $this->zipArchive = new ZipArchive;
        # share storage path
        $this->storagePath = storage_path('app/temp');
    }

    /**
     * Merge the document
     *
     * @param  array  $attributes
     *
     * @return $this?
     */
    public function merge($attributes = [])
    {
        # get Temporary Name
        $this->tempDocumentFilename = tempnam($this->storagePath, 'Document');
        # Copy the file to the temp destination
        File::copy($this->document->uri, $this->tempDocumentFilename);
        # Open Zip Archive
        $this->zipArchive->open($this->tempDocumentFilename);
        # set Slides
        $this->fill($attributes);
        # Close zip file
        if (false === $this->zipArchive->close()) {
            throw new Exception('Could not close zip file.');
        }
        # new file name is md5 + original file name
        $saveAs = md5(microtime()).File::basename($this->document->original_name);
        # Save the file with a new name
        return $this->save($saveAs);
    }

    /**
     * Save the temp File somewhere
     *
     * @param  string $saveAs
     * @return Document
     */
    public function save($saveAs)
    {
        # Move the file to the new location
        File::move($this->tempDocumentFilename, "{$this->storagePath}/{$saveAs}");
        # finalize
        $this->finalize($saveAs);
        # return
        return $this->document;
    }

    /**
     * Finalize the properties
     *
     * @param  string $saveAs
     * @return Void
     */
    protected function finalize($saveAs)
    {
        # overload the URI
        $this->document->uri = "{$this->storagePath}/{$saveAs}";
    }

    /**
     * Fill the document
     *
     * @param  array  $attributes
     * @return $this
     */
    protected function fill($attributes = [])
    {
        foreach($this->getSlides() as $slide){
            # take the XML
            $this->populate($slide, $attributes);
        }

        return $this;
    }

    /**
     * Populate each slide
     *
     * @param  array $slide
     * @param  array  $attributes
     *
     * @return array
     */
    protected function populate($slide, $attributes = [])
    {
        # Collect Attributes
        $attributes = collect($attributes);
        # pick the XML
        $xml = $this->zipArchive->getFromName($slide['name']);
        # replace
        preg_match_all($this->regexp, $xml, $matches);
        # replace the keys if found
        foreach($matches[1] as $key){
            $search = sprintf('${%s}', $key);
            $xml = str_replace($search, $attributes->get($key), $xml);
        }
        # Replace with new
        $this->zipArchive->addFromString($slide['name'], $xml);
    }

    /**
     * Populate slides
     *
     * @return array
     */
    protected function getSlides()
    {
        return is_null($this->slides) ? $this->slides = $this->loadSlides() : $this->slides;
    }

    /**
     * Iterate through the atchived files
     *
     * @return array
     */
    protected function loadSlides()
    {
        # reset slides
        $this->slides = [];
        # list all files
        for($i = 0; $i < $this->zipArchive->numFiles; $i++ ){
            # get the file
            $file = $this->zipArchive->statIndex($i);
            # get the slides by masp
            if(str_contains($file['name'], '/slides/')){
                # push the slide
                $this->slides[] = $file;
            }
        }

        return $this->slides;
    }
}
