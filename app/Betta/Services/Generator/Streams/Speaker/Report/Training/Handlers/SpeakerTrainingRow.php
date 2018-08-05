<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\Training\Handlers;

use Betta\Models\Training;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class SpeakerTrainingRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Training
     */
    protected $training;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'last_name',
        'first_name',
        'preferred_name_degree',
        'customer_master_id',
        'training_course_title',
        'brand_label',
        'effective_at',
        'expires_at',
        'status_label'
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile'
    ];

    /**
     * Create new Row instance
     *
     * @param Training $training
     */
    public function __construct(Training $training)
    {
        $this->training = $training;
    }

    /**
     * Profile of training
     *
     * @return Profile | null
     */
    public function getProfileAttribute()
    {
        return $this->training->profile;
    }

    /**
     * Last Name from Profile
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return $this->profile->last_name;
    }

    /**
     * First Name from Profile
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return $this->profile->first_name;
    }

    /**
     * Preferred Name and Degree from Profile
     *
     * @return string
     */
    public function getPreferredNameDegreeAttribute()
    {
        return $this->profile->preferred_name_degree;
    }

    /**
     * Customer Id from Profile
     *
     * @return string
     */
    public function getCustomerMasterIdAttribute()
    {
        return $this->profile->customer_master_id;
    }

    /**
     * training course title
     *
     * @return string
     */
    public function getTrainingCourseTitleAttribute()
    {
        return $this->training->training_course_title;
    }

    /**
     * brands label
     *
     * @return string
     */
    public function getBrandLabelAttribute()
    {
        return $this->training->brand_label;
    }

    /**
     * Effective Date of Training
     *
     * @return string
     */
    public function getEffectiveAtAttribute()
    {
        return excel_date($this->training->valid_from);
    }

    /**
     * Expiry Date of Training
     *
     * @return string
     */
    public function getExpiresAtAttribute()
    {
        return excel_date($this->training->valid_to);
    }

    /**
     * Expiry Date of Training
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->training->status_label;
    }
}
