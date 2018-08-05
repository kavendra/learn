<?php

namespace Betta\Services\Generator\Streams\Conference\NominationList\Handlers;

use Betta\Models\Profile;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Department',
        'Name',
        'Title',
        'Email',
        'Status',
    ];


    /**
     * Create new Row instance
     *
     * @param Profile $profile
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Get Department Name
     *
     * @return string | null
     */
    protected function getDepartmentAttribute()
    {
        return data_get($this->profile, 'pivot.groupName.label');
    }

    /**
     * Get Name
     *
     * @return string | null
     */
    protected function getNameAttribute()
    {
        return data_get($this->profile, 'preferred_name');
    }


     /**
     * Get Title
     *
     * @return string | null
     */
     protected function getTitleAttribute()
    {
        return data_get($this->profile, 'field_title');
    }

     /**
     * Get Email
     *
     * @return string | null
     */
     protected function getEmailAttribute()
    {
        return data_get($this->profile, 'primary_email');
    }

     /**
     * Get Status
     *
     * @return string | null
     */
    protected function getStatusAttribute()
    {
        return data_get($this->profile, 'pivot.nominationStatus.label');
    }

}
