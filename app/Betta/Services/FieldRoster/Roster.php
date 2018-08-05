<?php

namespace Betta\Services\FieldRoster;

use Betta\Foundation\Rosterize\AbstractRoster;
use Betta\Services\FieldRoster\FieldRosterDownloader as Downloader;

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
