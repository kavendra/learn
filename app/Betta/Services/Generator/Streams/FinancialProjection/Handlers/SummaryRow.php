<?php

namespace Betta\Services\Generator\Streams\FinancialProjection\Handlers;

use Betta\Models\Program;
use Betta\Models\NprCost;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class SummaryRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Carbon
     */
    protected $month;

    /**
     * Bind the implementation
     *
     * @var Collection
     */
    protected $programs;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Month',
        'No of Program',
        'Revenue',
        'NPR Cost',
        'Expenses',
        'Cash Flow',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
    ];

    /**
     * Create new Row instance
     *
     * @param Collection $collection
     */
    public function __construct(Carbon $month = null, Collection $programs = null, Collection $nprcosts = null)
    {
        $this->month    = $month ? : Carbon::now()->startOfMonth();
        $this->programs = $programs ? : collect();
        $this->nprcosts = $nprcosts ? : collect();
    }

    /**
     * Get Program Id
     *
     * @return string
     */
    public function getIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Month
     *
     * @return string
     */
    public function getMonthAttribute()
    {
        return $this->month->format('M y');
    }

    /**
     * Get No Of Programs
     *
     * @return int
     */
    public function getNoOfProgramAttribute()
    {
        return $this->programs->filter(function($program){
            return $program->start_date->between($this->month, $this->month->copy()->endOfMonth());
        })->count();
    }

    /**
     * Get Revenue
     *
     * @return float
     */
    public function getRevenueAttribute()
    {
        return $this->programs->filter(function($program){
            return $program->revenue_date->between($this->month, $this->month->copy()->endOfMonth());
        })->sum(function($program){
            return (new RevenueRow($program))->fill()->totalProgramCost;
        });
    }

    /**
     * Get Expenses
     *
     * @return float
     */
    public function getExpensesAttribute()
    {
        return $this->programs->filter(function($program){
            return $program->expenses_date->between($this->month, $this->month->copy()->endOfMonth());
        })->sum(function($program){
            return (new ExpensesRow($program))->fill()->totalProgramCost;
        });
    }

    /**
     * Get Expenses
     *
     * @return float
     */
    public function getNprcostAttribute()
    {
        return $this->nprcosts->filter(function($cost){
            return $cost->nprcost_date->between($this->month, $this->month->copy()->endOfMonth());
        })
        ->sum('estimate');
    }

    /**
     * Get Cash Flow
     *
     * @return float
     */
    public function getCashFlowAttribute()
    {
        return $this->attributes['Revenue'] + $this->attributes['NPR Cost'] - $this->attributes['Expenses'] ;
    }

}
