<?php

namespace Betta\Foundation\Helpers;

use Carbon\Carbon;
use PHPExcel_Shared_Date;
use Illuminate\Support\Facades\Blade;

class DateFormats
{
    /**
     * Convert the date to Excel
     *
     * @param  mixed $date
     * @param  mixed $default
     * @return float
     */
    public static function excelDate($date, $default = null)
    {
        # empty Date returns $default
        if (empty($date)) {
            return $default;
        }

        # reflect value
        $value = Carbon::parse($date)->format('U');

        # parse the date with PHPExcel_Shared_Date
        return PHPExcel_Shared_Date::FormattedPHPToExcel(
            date('Y', $value),
            date('m', $value),
            date('d', $value),
            date('H', $value),
            date('i', $value),
            date('s', $value)
        );
    }
}
