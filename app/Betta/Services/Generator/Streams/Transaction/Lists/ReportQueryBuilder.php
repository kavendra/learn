<?php

namespace Betta\Services\Generator\Streams\Transaction\Lists;

use App\Models\Conference as Model;

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
                    ->orderBy('exibitor_start_date');
    }
}
