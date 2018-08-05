<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Carbon\Carbon;
use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class ProgramCloseoutComplianceRow extends AbstratRowHandler
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
        '# of business days from program',
        'Program ID',
        'Brand',
        'Program Type',
        'Program Date',
        'Program Time',
        'Program Status',
        'Reconciled',
        'Representative Name',
        'District Manager Name',
        'Presentation Topic',
        'Location Name',
        'Location Address City',
        'Location Address State Province',
        'Speaker Name',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'field',
        'address',
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
     * Count the nubmer of the Business days from Program
     *
     * @return int
     */
    public function getOfBusinessDaysFromProgramAttribute()
    {
        return $this->program->start_date->diffInWeekdays(Carbon::now());
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
     * Formatted Time of the Program (formatting is done by Excel)
     *
     * @return string | null
     */
    public function getProgramTimeAttribute()
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
     * Yes if the Progtram is reconciled, otherwise No
     *
     * @return string | null
     */
    public function getReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
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
    public function getRepresentativeNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Primary Field's Name' Manager' name
     *
     * @return string | null
     */
    public function getDistrictManagerNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
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
     * Return The Location Address
     * @access hidden
     * @return Address
     */
    public function getAddressAttribute()
    {
        return $this->program->address;
    }

    /**
     * Location: Name
     *
     * @return string | null
     */
    public function getLocationNameAttribute()
    {
        return data_get($this->address, 'name');
    }

    /**
     * Location: City
     *
     * @return string | null
     */
    public function getLocationAddressCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Location: State
     *
     * @return string | null
     */
    public function getLocationAddressStateProvinceAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Program Speakers' names
     *
     * @return string
     */
    protected function getSpeakerNameAttribute()
    {
        return $this->program->primarySpeakers->implode('profile.preferred_name', ', ');
    }
}
