<?php

namespace Betta\Services\Generator\Streams\Management\Cost\WeeklySummary\Tabs\Taes;

use Betta\Models\ProgramType;
use Betta\Services\Generator\Foundation\BettaTab;

class Tab extends BettaTab
{
    /**
     * Title of the Worksheet
     *
     * @var string
     */
    protected $title = 'Train an Expert';

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
        'K' => self::AS_DATE,
        'L' => self::AS_TIME,
        'P:Q' => self::AS_NICE_INTEGER,
        'R' => self::AS_CURRENCY,
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
        # Get the data: in this case we want to slip resolution and instead chain more conditions
        $data = $this->builder($this->arguments, false)->byType([ProgramType::TAE])->get();
        # Transform
        return $this->data = $data->map(function($item){
            return with(new Handler($item))->fill();
        });
    }
}
