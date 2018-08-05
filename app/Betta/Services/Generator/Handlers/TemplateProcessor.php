<?php

namespace Betta\Services\Generator\Handlers;

use PhpOffice\PhpWord\TemplateProcessor as PhpWordTemplateProcessor;

class TemplateProcessor extends PhpWordTemplateProcessor
{
    protected $documentRels = 'word/_rels/document.xml.rels';

    /**
     * Get the Zip Class handler from the TemplateProcessor
     *
     * @return \PhpOffice\PhpWord\Shared\ZipArchive;
     */
    public function getZipClass()
    {
        return $this->zipClass;
    }

    /**
      * Set a new image
      *
      * @param string $search
      * @param string $replace
      */
     public function setImageValue($search, $replace)
     {
        // Sanity check
         if (!file_exists($replace)){
             return;
        }

        // Delete current image
        $this->getZipClass()->deleteName("word/media/{$search}");

        // Add a new one
        $this->getZipClass()->addFile($replace, "word/media/${search}");
    }


    /**
     * Replace the link in the Rels
     *
     * @param  placeholder to find $placeholder
     * @param  string $link
     * @return void
     */
    public function setLinkValue($placeholder, $link)
    {
        # Load the string
        $rels = $this->getZipClass()->getFromName( $this->documentRels );

        # Replace
        $rels = str_replace($placeholder, $link, $rels);

        # push back
        $this->getZipClass()->addFromString($this->documentRels, $rels);
    }
}
