<?php

namespace Betta\Services\Cancellation\ProductTheater;

use Betta\Models\ProgramStatus;

trait LevelOfWork
{
    /**
     * True if we have confirmed both Speaker and Venue
     *
     * @return boolean
     */
    protected function confirmedAudioLine()
    {
        # Speaker's lines are always confirmed
        return true;
    }

    /**
     * True if we have confirmed at least one Speaker
     *
     * @return boolean
     */
    protected function confirmedSpeaker()
    {
        return $this->program->confirmedSpeakers->isNotEmpty();
    }

    /**
     * True if the program has been claimed
     *
     * @return boolean
     */
    protected function initiated()
    {
        return ! in_array($this->program->program_status_id, [
            ProgramStatus::DRAFT,
            ProgramStatus::SUBMITTED,
            ProgramStatus::DENIED,
            ProgramStatus::APPROVED,
            ProgramStatus::PENDING_MANAGER,
            ProgramStatus::MANAGER_DENIED,
            ProgramStatus::MANAGER_APPROVED,
        ]);
    }
}
