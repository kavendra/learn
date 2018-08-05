<?php

namespace Betta\Models\Traits;

use Carbon\Carbon;

trait ValidTrait
{
    /**
     * Get the name of the column for applying the scope
     *
     * @return string
     */
    public function getValidFromColumn()
    {
        return defined('static::VALID_FROM_COLUMN') ? static::VALID_FROM_COLUMN : 'valid_from';
    }

    /**
     * Get the name of the column for applying the scope
     *
     * @return string
     */
    public function getValidToColumn()
    {
        return defined('static::VALID_TO_COLUMN') ? static::VALID_TO_COLUMN : 'valid_to';
    }

    /**
     * Get the fully qualified column name for applying the scope.
     *
     * @return string
     */
    public function getQualifiedValidFromColumn()
    {
        return $this->getTable().'.'.$this->getValidFromColumn();
    }

    /**
     * Get the fully qualified column name for applying the scope.
     *
     * @return string
     */
    public function getQualifiedValidToColumn()
    {
        return $this->getTable().'.'.$this->getValidToColumn();
    }

    /**
     * True if the record is valid currently
     *
     * @return boolean
     */
    public function getIsValidAttribute()
    {
        return $this->validAt( Carbon::now() );
    }

    /**
     * True is the record is not valid
     *
     * @return boolean
     */
    public function getIsNotValidAttribute()
    {
        return !$this->is_valid;
    }

    /**
     * Check if the record is valid at a given moment
     *
     * @param  Carbon $at
     * @param  string $validFromColumn
     * @param  string $validToColumn
     * @return boolean
     */
    public function validAt($at, $validFromColumn = null, $validToColumn = null)
    {
        # parse Carbon
        $at = Carbon::parse($at);
        # Compare the value
        return $this->validIntersect($at->copy()->startOfDay(), $at->copy()->endOfDay(), $validFromColumn, $validToColumn);
    }

    /**
     * Check if the record valid period intersects with start and end dates
     *
     * @param  Carbon $start
     * @param  Carbon $end
     * @param  string|null $validFromColumn
     * @param  string|null $validToColumn
     * @return boolean
     */
    public function validIntersect(Carbon $start, Carbon $end, $validFromColumn = null, $validToColumn = null)
    {
        # get comparison values
        # These values could be empty, BTW
        $validFrom = object_get($this, $validFromColumn ?: $this->getValidFromColumn());
        $validTo   = object_get($this, $validToColumn ?: $this->getValidToColumn());

        # Coalesce the validFrom value to indefinitely in the past
        $validFrom =   $validFrom ?: Carbon::minValue();

        # if the validTo not set, its valid forever
        $validTo   =   $validTo ?: Carbon::maxValue();

        # Valid Period should begin before end and end after start:
        return Carbon::parse($validFrom)->lte($end) and Carbon::parse($validTo)->gte($start);
    }

    /**
     * Apply Valid Scope
     *
     * @param  Builder $query
     * @param  Integer $at
     * @return Builder
     */
    public function scopeValid($query, $at = null)
    {
        # Default Time
        $at = empty($at) ? Carbon::now() : Carbon::parse($at);

        # get column names
        $valid_from     = $this->getModel()->getQualifiedValidFromColumn();
        $valid_to       = $this->getModel()->getQualifiedValidToColumn();

        # Apply the scope
        return $query->whereRaw("'{$at}' BETWEEN COALESCE({$valid_from}, '1901-01-01') AND COALESCE({$valid_to}, '2038-01-18')");
    }

    /**
     * Scope records valid Before
     *
     * @param  Buidler $query
     * @param  Carbon $date
     * @param  null|string $field
     * @return Buidler
     */
    public function scopeValidBefore($query, $date, $field = null)
    {
        $field = empty($field) ? $this->getModel()->getQualifiedValidToColumn() : $field;

        # what's the greatest Value?
        $max = Carbon::maxValue();

        # Apply the scope
        return $query->whereRaw("'{$date}' >= COALESCE({$field}, '{$max}')");
    }

    /**
     * Scope records valid After
     *
     * @param  Buidler $query
     * @param  Carbon $date
     * @param  null|string $field
     * @return Buidler
     */
    public function scopeValidAfter($query, $date, $field = null)
    {
        $field = empty($field) ? $this->getModel()->getQualifiedValidFromColumn() : $field;

        # what's the smallest Value?
        $min = Carbon::minValue();

        # Apply the scope
        return $query->whereRaw("'{$date}' <= COALESCE({$field}, '{$min}')");
    }

    /**
     * Crisscorss valid selection
     *
     * @param  Builder   $query
     * @param  array|int $inYear
     * @return Builder
     */
    public function scopeValidBetween($query, Carbon $start, Carbon $end)
    {
        return $query->where(function ($q) use ($start, $end) {
                $q->where($this->getQualifiedValidFromColumn(), '<=', $end)
                  ->where($this->getQualifiedValidToColumn(), '>=', $start);
            });
    }

    /**
     * Crisscorss valid selection
     *
     * @param  Builder   $query
     * @param  array|int $inYear
     * @return Builder
     */
    public function scopeValidInFiscalYears($query, $inYear)
    {
        return $query->where(function ($advanced) use ($inYear) {
            foreach ((array) $inYear as $year) {
                    # Any of the following must be true
                $advanced->orWhere(function ($q) use ($year) {

                          # Both of the following must be true
                          $q->where($this->getQualifiedValidFromColumn(), '<=', $this->getFiscalYearEnd($year))
                            ->where($this->getQualifiedValidToColumn(), '>=', $this->getFiscalYearStart($year));
                });
            }
        });
    }

    /**
     * Crisscorss valid selection
     *
     * @param  Builder   $query
     * @param  array|int $inYear
     * @return Builder
     */
    public function scopeValidInCalendarYears($query, $inYear)
    {
        return $query->where(function ($advanced) use ($inYear) {
            foreach ((array) $inYear as $year) {
                    # Any of the following must be true
                $advanced->orWhere(function ($q) use ($year) {
                          # Both of the following must be true
                          $q->where($this->getQualifiedValidFromColumn(), '<=', $this->getCalendarYearEnd($year))
                            ->where($this->getQualifiedValidToColumn(), '>=', $this->getCalendarYearStart($year));
                });
            }
        });
    }

    /**
     * Calendar Year begins in January 1, $year
     *
     * @param  int $year
     * @return Carbon
     */
    public function getCalendarYearStart($year)
    {
        return Carbon::parse("January 01 {$year}");
    }

    /**
     * Fiscal Year ends in December 31, $year, end of day
     *
     * @param  int $year
     * @return Carbon
     */
    public function getCalendarYearEnd($year)
    {
        return $this->getCalendarYearStart($year)->endOfYear();
    }

    /**
     * Fiscal Year begins in April 1, $year
     *
     * @param  int $year
     * @return Carbon
     */
    public function getFiscalYearStart($year)
    {
        return Carbon::parse("April 01 {$year}");
    }

    /**
     * Fiscal Year end in April 1, $year + 1 - less i second
     *
     * @param  int $year
     * @return Carbon
     */
    public function getFiscalYearEnd($year)
    {
        return $this->getFiscalYearStart($year)->addYear()->subSecond();
    }

    /**
     * Get Fiscal Year of the current date
     *
     * @param  mixed $date
     * @return int
     */
    public function getFiscalYear($date)
    {
        # what's the year of the dat?
        $year = Carbon::parse($date)->year;

        # evaluate
        return  $date->lt($this->getFiscalYearStart($year)) ? $year-1 : $year;
    }

    /**
     * Make a period attribute
     *
     * @param  mixed $from
     * @param  mixed $to
     * @return string
     */
    public function makePeriodAttribute($from, $to, $format = 'M j, Y', $divider = '&rarr;', $empty = '&hellip;')
    {
        # Parse the dates to format or ''
        $from = empty($from) ? '' : Carbon::parse($from)->format($format);
        $to = empty($to) ? '' : Carbon::parse($to)->format($format);

        # Initial Result
        $result = $from;

        # If both are present
        if ($from AND $to){
            $result .= " &rarr; ";
        } elseif ($from){
            $result .= " &hellip; ";
        }

        # add to
        if ($to){
            $result .= "{$to}";
        }

        return $result;
    }
}
