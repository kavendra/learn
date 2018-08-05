<?php

namespace Betta\Services\Generator\Streams\Usage\IsigninApp\Handlers;

use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class Row extends AbstratRowHandler
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
        'Program Label',
        'Program Date',
        'Account Manager',
        'Program Type',
        'Program Status',
        'Brand',
        'Food Served',
        'Closed Out',
        'SignIn Type',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
    ];

    /**
     * Create new Row instance
     *
     * @param Collection $collection
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Get Program Id
     *
     * @return string
     */
    public function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Program Label
     *
     * @return string
     */
    public function getProgramLabelAttribute()
    {
        return $this->program->label;
    }

    /**
     * Get Program start date
     *
     * @return float
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Program account manager
     *
     * @return string
     */
    public function getAccountManagerAttribute()
    {
        return data_get($this->program, 'primary_field.preferred_name');
    }

    /**
     * Get Program Type
     *
     * @return float
     */
    public function getProgramTypeAttribute()
    {
        return data_get($this->program, 'programType.label');
    }

    /**
     * Get Program Status
     *
     * @return float
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
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
     * Food served
     *
     * @return string
     */
    public function getFoodServedAttribute()
    {
        return $this->program->fb_costs->sum('calculated') ?: 'No';
    }

    /**
     * Closed Out
     *
     * @return string
     */
    public function getClosedOutAttribute()
    {
        return null;
        //return empty($this->program->closeout->certify) ? '' : 'Yes';
    }

    /**
     * SignIn Type
     *
     * @return string
     */
    public function getSignInTypeAttribute()
    {
        return $this->program->registrations->sum('has_signature') > 0 ? 'iSignIn App' : 'Portal';
    }
}
