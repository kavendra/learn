<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\Taes;

use Betta\Services\Generator\Streams\Programs\Report\SpeakerProgramTracker\Handlers\RowHandler;

class Handler extends RowHandler
{
    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Brand Name',
        'Presentation Topic',
        'Program ID',
        'Program Status',
        'Is Reconciled',
        'Sales Representative',
        'Manager',
        'National Sales Director',
        'Program Type',
        'Speaker Name',
        'Program Date',
        'Program Time',
        'Venue Name',
        'Venue City',
        'Venue State',
        'Total Attendees',
        'Total Horizon Employees',
        'Total Program Cost',
        'Food Beverage Receipt Received',
        'Attendee Signin Sheet Received',
        'Cancel Comments',
    ];

    /**
     * Make sure we are adding the progrm attribute
     *
     * @return Betta\Models\Program
     */
    public function getProgramAttribute()
    {
        # Mark attribute hidden
        $this->hidden[] = 'program';
        # return
        return $this->program;
    }

    /**
     * Make sure we are adding the progrm attribute
     *
     * @return Betta\Models\Program
     */
    public function getTotalRealCostAttribute()
    {
        # Mark attribute hidden
        $this->hidden[] = 'total_actual_cost';
        # return
        return $this->program->costs->sum('real');
    }

    /**
     * Make sure we are adding the progrm attribute
     *
     * @return Betta\Models\Program
     */
    public function getTotalAllocatedCostAttribute()
    {
        # Mark attribute hidden
        $this->hidden[] = 'total_allocated_cost';
        # return
        return $this->program->costs->sum('allocated');
    }
}
