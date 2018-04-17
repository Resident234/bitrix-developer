<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;

class FilesHelper
{
    /**
     * @param $pathToFile
     * @return mixed
     */
    public static function getFileExt($pathToFile)
    {
        $arPathToFile = explode(".", $pathToFile);
        return $arPathToFile[count($arPathToFile) - 1];
    }
}