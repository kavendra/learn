<?php

namespace Betta\Services\FieldRoster;

use Betta\Foundation\Rosterize\AbstractRosterDownloader;
use Betta\Services\FieldRoster\FieldRosterFile as RosterFile;

class FieldRosterDownloader extends AbstractRosterDownloader
{
    /**
     * Drive to look the files for
     *
     * @var string
     */
    protected $disk = 'local';


    /**
     * Path to look for the files
     *
     * @var string
     */
    protected $storagePath = 'import';


    /**
     * Store the pattern for the feed file
     *
     * @var string
     */
    protected $pattern = '%year%_%month%_%day% Field Roster.xlsx';


    /**
     * Create Roster File
     *
     * @param RosterFile $roster
     */
    public function __construct(RosterFile $roster)
    {
        $this->roster = $roster;
    }
}
