<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Program;
use Betta\Models\Registration;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class SummaryHcpRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        '# of Programs Attended',
        'Customer Master ID',
        'NPI',
        'Attendee Name',
        'Brand',
        'Presentation Topic',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
        'field',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Collection $registrations)
    {
        $this->registration = $registrations->first();
    }

    /**
     * Program
     *
     * @access hidden
     * @return Program | null
     */
    public function getProgramAttribute()
    {
        return data_get($this->registration,'program');
    }

    public function getOfProgramsAttendedAttribute()
    {
        return $this->registration->program_count;
    }

    /**
     * Customer Master ID
     *
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return data_get($this->registration,'profile.customer_master_id');
    }

    /**
     * NPI
     *
     * @return string
     */
    public function getNpiAttribute()
    {
        return $this->registration->npi;
    }

    /**
     * Customer Master ID
     *
     * @return string
     */
    public function getAttendeeNameAttribute()
    {
        return $this->registration->preferred_name;
    }

    /**
     * Program brand
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->registration->program,'primary_brand.label');
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getPresentationTopicAttribute()
    {
        return $this->registration->program->title;
    }
}
