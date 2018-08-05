<?php

namespace Betta\Services\Generator\Interfaces;

interface ExcelGeneratorInterface
{


    /**
     * Produce the report
     *
     * @return
     * @author ZR
     */
    protected function process();


    /**
     * Load the Actual Data
     *
     * @param  array $arguments
     * @return Collection
     * @author ZR
     */
    protected function loadMergeData($arguments);


    /**
     * Match columns to formats
     *
     * @return array
     * @author ZR
     */
    protected function getFormats();
}
