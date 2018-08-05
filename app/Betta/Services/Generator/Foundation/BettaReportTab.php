<?php

 namespace Betta\Services\Generator\Foundation;

use Maatwebsite\Excel\Excel;

abstract class BettaReportTab
{
    /**
     * Store the Upper case Report in case we need to loop back or eager load values
     *
     * @var Betta\Services\Generator\Foundation\AbstractReport
     */
    protected $parent;

    /**
     * Create new Instance of class
     *
     * @param AbstractReport $parent
     */
    public function __construct(AbstractReport $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Handle the tab
     *
     * @return
     */
    public function handle()
    {
        return '';
    }
}
