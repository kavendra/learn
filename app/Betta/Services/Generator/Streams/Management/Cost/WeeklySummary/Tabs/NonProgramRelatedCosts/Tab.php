<?php
namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\NonProgramRelatedCosts;

use Carbon\Carbon;
use Betta\Services\Generator\Foundation\BettaTab;

class Tab extends BettaTab
{
    /**
     * Title of the Worksheet
     *
     * @var string
     */
    protected $title = 'Non-Program Costs';

    /**
     * Define  the builder for the tab
     *
     * @var string
     */
    protected $builder = Builder::class;

    /**
     * Formats implementing
     *
     * @see   Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var   array
     */
    protected $formats = [
        'B' => self::AS_DATE,
        'D' => self::AS_CURRENCY,
    ];

    /**
     * Resolve the data for the tab
     *
     * @return array | Illuminate\Support\Collection
     */
    public function getData()
    {
        if(!empty($this->data)){
            return $this->data;
        }
        # Get the data
        return $this->data = $this->builder($this->arguments)->map(function($item){
            return with(new Handler($item))->fill();
        });
    }
}
