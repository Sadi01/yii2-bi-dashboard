<?php


namespace sadi01\bidashboard\helpers;

use Yii;

/**
 * @author SADi <sadshafiei.01@gmail.com>
 */
class CoreHelper
{
    public static function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }

    public static function getStartAndEndOfDay($time_zone = 'Asia/Tehran', $time = null)
    {
        date_default_timezone_set($time_zone);
        $time = $time ?: time();
        $start = strtotime("today", $time);
        $end   = strtotime("tomorrow", $start) - 1;

        return [
            'start' => $start,
            'end'   => $end,
        ];
    }

    public static function getMonthsCountBetweenTwoDays($min_day, $max_day)
    {
        $min_day_parse = date_parse($min_day);
        $max_day_parse = date_parse($max_day);

        $min_year = $min_day_parse['year'];
        $max_year = $max_day_parse['year'];

        $min_month = $min_day_parse['month'];
        $max_month = $max_day_parse['month'];

        return (($max_year - $min_year) * 12) + ($max_month - $min_month);
    }

    public static function getMonth($time = null)
    {
        return Yii::$app->pdate->jdate('m', $time ?: time(), '', 'Asia/Tehran', 'en');
    }

    public static function getCurrentMonth()
    {
        return self::getMonth();
    }

    public static function getYear($time = null)
    {
        return Yii::$app->pdate->jdate('Y', $time ?: time(), '', 'Asia/Tehran', 'en');
    }

    public static function getCurrentYear()
    {
        return self::getYear();
    }

    public static function getDay($time = null)
    {
        return Yii::$app->pdate->jdate('d', $time ?: time(), '', 'Asia/Tehran', 'en');
    }

    public static function getCurrentDay()
    {
        return self::getDay();
    }
}