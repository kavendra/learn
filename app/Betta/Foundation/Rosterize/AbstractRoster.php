<?php

namespace Betta\Foundation\Rosterize;

use Betta\Foundation\HasMessageBag;
use Betta\Foundation\Rosterize\AbstractRosterDownloader;

abstract class AbstractRoster
{
    use HasMessageBag;

    /**
     * Create instance of Downlaoder
     *
     * @var Betta\Services\FieldRoster\FieldRosterDownloader
     */
    protected $downloader;

    /**
     * Create instance of the Clas
     *
     * @param  Downloader $downloader
     * @return Void
     */
    public function __construct(AbstractRosterDownloader $downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * Complete the Roster Run for all available rosters
     *
     * @return Illuminate\Support\MessageBag
     */
    public function run()
    {
        $this->getFeeds()->each(function($roster){
            $this->getMessageBag()->add( $roster->getPath(), $roster->run() );
        });

        return $this->getMessageBag();
    }

    /**
     * Get the Feeds from the Downloader
     *
     * @return Collection
     */
    protected function getFeeds()
    {
        return $this->downloader->get();
    }
}
