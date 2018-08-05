<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;
use Carbon\Carbon;

class MultipleRepsRow extends AbstratRowHandler
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
        'Program ID',
        'Program Date',
        'Representative Names',
        'Brand',
        'Program Type',
        'Presentation Topic',
        'Program Status',
        'HCP Attendees',
        'Representative Attendees',

    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'field',

    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Program ID
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Program brand
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return data_get($this->program,'primary_brand.label');
    }

    /**
     * Program type
     *
     * @return string | null
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

     /**
     * Formatted Date of the Program
     *
     * @return string | null
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Program Status
     *
     * @return string | null
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Program Primary Field
     *
     * @access hidden
     * @return Profile | null
     */
    public function getFieldAttribute()
    {
        return $this->program->primary_field;
    }

    /**
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getRepresentativeNamesAttribute()
    {
        return $this->program
                    ->field_registrations
                    ->where('attended', true)
                    ->sortBy('last_name')
                    ->implode('preferred_name', ', ');
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getPresentationTopicAttribute()
    {
        return $this->program->title;
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getHcpAttendeesAttribute()
    {
        return $this->program->hcp_registrations->where('attended', true)->count();
    }

    /**
     * Total Attendee count
     *
     * @return string | null
     */
    public function getRepresentativeAttendeesAttribute()
    {
        return $this->program->field_registrations->where('attended', true)->count();
    }

}
