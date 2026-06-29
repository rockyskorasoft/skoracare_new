<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    private static $carbonClass = Carbon::class;


    /**
     * function to get the current date and time
     *
     * @param string $format
     * @return string
     */
    public static function getCurrentDateTime($format = '')
    {
        $format = empty($format) ? config('constants.date_format') : $format;
        return self::$carbonClass::now()->format($format);
    }

    /**
     * function to format the date and time
     *
     * @param Object $dateTime
     * @param string $format
     * @return string
     */
    public static function formatDateTime($dateTime, string $format = '')
    {
        $format = empty($format) ? config('constants.date_format') : $format;
        $dateTime = self::$carbonClass::parse($dateTime);
        return $dateTime->format($format);
    }

    /**
     * function to parse the datetime
     *
     * @param Object $dateTime
     * @return Object
     */
    public static function parseDateTime($dateTime)
    {
        return self::$carbonClass::parse($dateTime);
    }
}
