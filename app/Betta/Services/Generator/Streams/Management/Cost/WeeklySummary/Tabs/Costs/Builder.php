<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\Costs;

use Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * List the relations
     *
     * @var array
     */
    protected $with = [
        'costs.context',
        'costs.payee.profile',
    ];
}
