<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\Programs;

use Betta\Models\ProgramType;
use Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * Exclude the status
     *
     * @var array
     */
    protected $excludeTypes = [
        ProgramType::TAE,
    ];

    /**
     * List the relations
     *
     * @var array
     */
    protected $with = [
        'costs',
        'cancellations',
        'registrations',
        'presentations',
        'presentationTopics',
        'brands.programTypes',
        'closeout.certifications',
        'programSpeakers.profile',
        'programCaterers.documents',
        'programLocations.address',
        'programLocations.documents',
        'fields.territories.parent.primaryProfiles.territories',
    ];
}
