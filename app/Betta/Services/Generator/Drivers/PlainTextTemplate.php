<?php

namespace Betta\Services\Generator\Drivers;

use View;
use Illuminate\Filesystem\Filesystem;
use Betta\Services\Generator\Interfaces\TemplateInterface;

class PlainTextTemplate implements TemplateInterface
{
    /**
     * Result could be converted to Document
     */
    use DocumentTrait;

    /**
     * Share the Tempalte
     * @var TemplateProcessor
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
     * @var string
     */
    public $stream;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Bind the implementation of the View
     *
     * @var View
     */
    protected $compiler;

    /**
     * Class Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->template = $path;
        $this->filesystem = new Filesystem;
    }

    /**
     * Return the path of the template
     *
     * @return string
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
     * @return
     */
    public function merge($data = array())
    {
        $this->stream = $this->render($data);

        return $this;
    }

    /**
     * Save the template somewhere
     *
     * @param  string $saveAs
     * @return Object
     */
    public function save($saveAs)
    {
        $this->filesystem->put($saveAs, $this->stream);

        $this->finalize($saveAs);

        return $this;
        #return (object)[
        #    'path'   => $saveAs,
        #    'file'   => $saveAs without md5,
        #    'stream' => $this->filesystem->get($saveAs)
        #];
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
    }

    /**
     * Render string
     *
     * @param  array $data
     * @return string
     */
    protected function render($data = [])
    {
        return $this->getCompiler()->with($data)->render();
    }

    /**
     * Make Compiler
     *
     * @return View
     */
    protected function getCompiler()
    {
        return $this->compiler ?: $this->setCompiler();
    }

    /**
     * Make Compiler
     *
     * @return View
     */
    protected function setCompiler()
    {
        return $this->compiler = View::make( $this->getTemplate() );
    }
}
