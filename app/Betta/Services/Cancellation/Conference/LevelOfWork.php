<?php

namespace Betta\Services\Cancellation\Conference;

use Betta\Models\ConferenceStatus;

trait LevelOfWork
{
    /**
     * True if we have confirmed both Speaker and Venue
     *
     * @return boolean
     */
    protected function confirmedSpeakerAndVenue()
    {
        return true;
    }

    /**
     * True if we have confirmed at least one Speaker
     *
     * @return boolean
     */
    protected function confirmedSpeaker()
    {
        return true;
    }

    /**
     * True if the conference has been claimed
     *
     * @todo  List all the statuses BEFORE the claimed (DO NOT INCLUDE CANCELLED)
     * @return boolean
     */
    protected function initiated()
    {
        return ! in_array($this->conference->conference_status_id, [
            ConferenceStatus::DRAFT,
        ]);
    }
}
