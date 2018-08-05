<?php

namespace Betta\Foundation\Rosterize;

use Storage;
use Betta\Foundation\Rosterize\AbstractRosterFeed as RosterFile;

abstract class AbstractRosterDownloader
{
    /**
     * Drive to look the files for
     *
     * @var string
     */
    protected $disk;


    /**
     * Path to look for the files
     *
     * @var string
     */
    protected $storagePath;


    /**
     * Store the pattern for the feed file
     *
     * @var string
     */
    protected $pattern;


    /**
     * Obtain the list of the Feed files, conforming to the pattern
     *
     * @return Collection
     */
    public function get()
    {
        # List all files at pth, conforming to pattern,
        # Iterate, make each a Roster
        return $this->getFiles()->map(function($file){
            return $this->makeRoster(Storage::disk($this->disk), $file);
        });
    }


    /**
     * Collect all files at path
     *
     * @return Collection
     */
    protected function getFiles()
    {
        $files = array_filter(Storage::disk($this->disk)->files( $this->storagePath ), function ($file){
            return str_is( $file, $this->getFileName() );
        });

        return collect($files);
    }


    /**
     * Return the file name pattern to compare against
     *
     * @return string
     */
    protected function getFileName()
    {
        # Get the pattern
        $filename = $this->pattern;

        # replace the pattern
        $filename = str_replace('%year%', date('Y'), $filename);

        # replace the pattern
        $filename = str_replace('%month%', date('m'), $filename);

        # replace the pattern
        $filename = str_replace('%day%', date('d'), $filename);

        return "{$this->storagePath}/{$filename}";
    }


    /**
     * Make Roster out of the file
     *
     * @return AbstractRosterFeed
     */
    protected function makeRoster($filesystem, $path)
    {
        return $this->roster->setFilesystem($filesystem)->setPath($path);
    }
}
