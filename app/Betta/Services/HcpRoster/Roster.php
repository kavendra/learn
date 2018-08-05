<?php

namespace Betta\Services\HcpRoster;

use Betta\Foundation\Rosterize\AbstractRoster;
use Betta\Services\HcpRoster\HcpRosterDownloader as Downloader;

class Roster extends AbstractRoster
{
    /**
     * Create instance of the Class
     *
     * @param  Downloader $downloader
     * @return Void
     */
    public function __construct(Downloader $downloader)
    {
        $this->downloader = $downloader;
    }
}
