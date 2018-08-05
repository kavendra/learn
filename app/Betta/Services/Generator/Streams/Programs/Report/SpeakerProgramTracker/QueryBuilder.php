<?php

namespace Betta\Services\Generator\Streams\Programs\Report\SpeakerProgramTracker;

use Betta\Models\Program;
use Betta\Models\ProgramStatus;

trait QueryBuilder
{
    /**
     * Get the Builder
     *
     * @return Collection
     */
    public function getBuilder($arguments)
    {
        return resolve(Program::class)->anyReport($arguments)->speakerPrograms()->noTest()->notInStatus([
            ProgramStatus::DRAFT,
            ProgramStatus::DENIED,
            ProgramStatus::MANAGER_DENIED,
        ])->orderBy('start_date');
    }
}
