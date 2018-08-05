<?php

namespace Betta\Services\Generator;

use File;
use Betta\Models\Document;

trait Merges
{
    /**
     * Map handler classes to the File extension
     *
     * @var array
     */
    protected $handlers = [
        'pptx' => Handlers\PptxHandler::class,
    ];

    /**
     * Get the handler for the Document
     *
     * @param  Document $document
     * @return mixed implementations of TemplateDriver
     */
    protected function getHandler(Document $document)
    {
        if($handler = array_get($this->handlers, File::extension($document->uri))){
            return app($handler, compact('document'));
        }
    }

    /**
     * Populate the Document with Data, if can resolve handler
     *
     * @param  Document $document
     * @param  array    $attributes
     * @return Document
     */
    protected function merge(Document $document, $attributes = [])
    {
        if($handler = $this->getHandler($document)){
            return $handler->merge($attributes);
        }
        # return document
        return $document;
    }
}
