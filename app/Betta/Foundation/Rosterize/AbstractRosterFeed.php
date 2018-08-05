<?php

namespace Betta\Foundation\Rosterize;

use Betta\Foundation\HasMessageBag;
use Illuminate\Filesystem\FilesystemAdapter;

abstract class AbstractRosterFeed
{
    use HasMessageBag;

    /**
     * Bind the implementation of
     *
     * @var Storage
     */
    protected  $filesystem;


    /**
     * Filename of the roster file
     *
     * @var string
     */
    protected  $rosterFile;


    /**
     * Run the Roster
     *
     * @return Collection
     */
    abstract function run();


    /**
     * Set Filesystem
     *
     * @param Illuminate\Filesystem\FilesystemAdapter $filesystem
     * @return Instance
     */
    public function setFilesystem(FilesystemAdapter $filesystem)
    {
        $this->filesystem = $filesystem;

        # Chain instance
        return $this;
    }


    /**
     * Set the Path for the Roster
     *
     * @param string $path
     * @return Instance
     */
    public function setPath($path)
    {
        $this->path = $path;

        # Chain instance
        return $this;
    }


    /**
     * Return Filesystem
     *
     * @return Illuminate\Filesystem\FilesystemAdapter | null
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }


    /**
     * Return full path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Resolve the location and return the file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->getStoragePath().$this->path;
    }


    /**
     * Return results, whatever they may be
     *
     * @return Collection
     */
    public function getResults()
    {
        return $this->getMessageBag()->getMessages();
    }


    /**
     * Resolve Storage Path
     *
     * @return string
     */
    protected function getStoragePath()
    {
        return $this->filesystem->getDriver()->getAdapter()->getPathPrefix();
    }

}
