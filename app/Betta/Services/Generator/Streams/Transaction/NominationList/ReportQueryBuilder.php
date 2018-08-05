<?php

namespace Betta\Services\Generator\Streams\Conference\NominationList;

use Betta\Models\Conference as Model;

trait ReportQueryBuilder
{
    /**
     * Get the Builder
     *
     * @return Builder
     */
    public function getBuilder($arguments)
    {
        return Model::anyReport($arguments)
                    ->orderBy('start_date');
    }
}
