<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance\Handlers;

use Betta\Models\Program;

class OneAttendeeRow extends OfficeStaffRow
{
    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Program Date',
        'Representative',
        'Profile ID',
        'Brand',
        'Program Type',
        'Presentation Topic',
        'Status',
        'Customer Master ID',
        'NPI',
        'Attendee Name',
        'Degree',
        'Specialty',
        'Address',
        'City',
        'State',
        'Zip',
        'Email',
        'Phone',
    ];

    /**
     * get Registration model of HCP registration
     *
     * @access hidden
     * @return Registration | null
     */
    public function getRegistrationAttribute()
    {
        return $this->program->hcp_registrations->where('attended', true)->first();
    }

    /**
     * Degree
     *
     * @return string | null
     */
    public function getDegreeAttribute()
    {
        return object_get($this->registration, 'porzio_degree');
    }

}
