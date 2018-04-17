<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */



class DateTimeHelper
{
    /**
     * Помощник форматирования даты/времени в человекоудобный формат
     *
     * @param string $sDate
     * @param string $sFormatOut
     * @param bool   $sFormatIn
     *
     * @return bool|string
     */
    public static function dateHumanitized($sDate, $sFormatOut = 'd F', $sFormatIn = false)
    {

        $sResult = false;

        if (!$sFormatIn) {
            $sFormatIn = FORMAT_DATETIME;
        }

        if (strlen($sDate)) {

            $sResult = FormatDate($sFormatOut, MakeTimeStamp($sDate, $sFormatIn));
        }

        return $sResult;
    }


    /**
     * @param $time
     * @return int
     */
    public static function timeToMinutes($time)
    {
        list($hours, $minutes) = explode(':', $time);
        return $hours * 60 + $minutes;
    }


}