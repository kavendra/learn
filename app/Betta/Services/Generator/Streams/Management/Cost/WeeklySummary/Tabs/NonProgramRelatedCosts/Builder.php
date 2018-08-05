<?php
namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\NonProgramRelatedCosts;

use Carbon\Carbon;
use Betta\Models\NprCost;

class Builder
{

    /**
     * List the relations
     *
     * @var array
     */
    protected $with = [
        #'costItem.nprCostCategories',
        #'brand'
    ];

    /**
     * Construct the class
     *
     * @param Program $program
     */
    public function __construct(NprCost $nprCost)
    {
        $this->nprCost = $nprCost;
    }

    /**
     * Get the Builder
     *
     * @return Collection
     */
    public function make(array $arguments)
    {
        return $this->nprCost
                    ->anyReport( $arguments )
                    ->with($this->with);
    }
}
