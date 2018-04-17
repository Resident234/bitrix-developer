<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Loader;

class LogHelper
{

    /**
     * запись строковых данных в лог
     * @param $str
     * @param $fileName
     * @param $path
     */
    public static function addToLog($str, $fileName = "log.txt", $path = "/")
    {
        $res = file_put_contents(
            $_SERVER["DOCUMENT_ROOT"] . $path . $fileName,
            "[" . date("Y-m-d H:i:s") . "] " . $str . " \n",
            FILE_APPEND
        );

    }





}