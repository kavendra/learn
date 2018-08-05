<?php

namespace Betta\Services\Generator\Streams\Exceptions;

use Betta\Anomaly\Streams\Grid\Report;

class GridReport extends Report
{
    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;
}
