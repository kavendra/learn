<?php

namespace Betta\Services\HcpRoster;

use Betta\Foundation\Rosterize\AbstractRosterDownloader;
use Betta\Services\HcpRoster\HcpRosterFile as RosterFile;

class HcpRosterDownloader extends AbstractRosterDownloader
{
    /**
     * Drive to look the files for
     *
     * @var string
     */
    protected $disk = 'roster';

    /**
     * Path to look for the files
     *
     * @var string
     */
    protected $storagePath = 'incoming';

    /**
     * Store the pattern for the feed file
     *
     * @var string
     */
    protected $pattern = 'HZNP_HCP_UNIVERSE_FILE_%month%%day%%year%.txt';

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
