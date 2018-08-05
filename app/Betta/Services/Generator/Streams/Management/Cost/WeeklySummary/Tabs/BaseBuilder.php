<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs;

use Betta\Models\Program;
use Betta\Models\ProgramType;
use Betta\Models\ProgramStatus;
use Betta\Services\Generator\Foundation\BettaTabBuilder;

class BaseBuilder extends BettaTabBuilder
{
    /**
     * Exclude the status
     *
     * @var array
     */
    protected $excludeStatuses = [
        ProgramStatus::DRAFT,
        ProgramStatus::DENIED,
        ProgramStatus::MANAGER_DENIED,
    ];

    /**
     * Exclude the status
     *
     * @var array
     */
    protected $excludeTypes = [];

    /**
     * List the relations
     *
     * @var array
     */
    protected $with = [];

    /**
     * Construct the class
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Get the Builder
     *
     * @return Collection
     */
    public function make($arguments)
    {
        return $this->program
                    ->noTest()
                    ->notByType($this->excludeTypes)
                    ->notInStatus($this->excludeStatuses)
                    ->anyReport($arguments)
                    ->orderBy('start_date')
                    ->with($this->with);
    }
}
