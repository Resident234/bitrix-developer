<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Loader;

class CommonHelper
{

    const EARTH_RADIUS = 6372795; // Радиус земли

    /**
     * Returns right word plural form
     * @param int $count
     * @param string $form1
     * @param string $form2_4
     * @param string $form5_0
     * @return string
     *
     * пример вызова
     * $arWords = array(
     * array("товар", "товара", "товаров"),
     * array("матрас", "матраса", "матрасов"),
     * array("кровать", "кровати", "кроватей"),
     * array("шкаф", "шкафа", "шкафов"),
     * array("коллекция", "коллекции", "коллекций")
     * );
     *
     * foreach($arWords as $keyWords => $itemWords){
     * Debug( CommonHelper::getFormattedEnding(rand(0, 1000), $itemWords[0], $itemWords[1], $itemWords[2]) );
     * }
     */
    public static function getFormattedEnding($count, $form1 = "балл", $form2_4 = "балла", $form5_0 = "баллов")
    {

        $strForm = "";

        $n100 = $count % 100;
        $n10 = $count % 10;


        if (($n100 > 10) && ($n100 < 21)) {
            $strForm = $form5_0;
        } else if ((!$n10) || ($n10 >= 5)) {
            $strForm = $form5_0;
        } else if ($n10 == 1) {
            $strForm = $form1;
        }

        if (!$strForm) $strForm = $form2_4;


        return $strForm;

    }


    /**
     * Форматирует окончание в соответствии с переданным числом
     * @param $iNumber
     * @param $aEndings = array('книга', 'книги', 'книг')
     *
     * @return mixed
     */
    public static function getNumEnding($iNumber, $aEndings)
    {
        $iNumber = $iNumber % 100;
        if ($iNumber >= 11 && $iNumber <= 19) {
            $sEnding = $aEndings[2];
        } else {
            $i = $iNumber % 10;
            switch ($i) {
                case (1):
                    $sEnding = $aEndings[0];
                    break;
                case (2):
                case (3):
                case (4):
                    $sEnding = $aEndings[1];
                    break;
                default:
                    $sEnding = $aEndings[2];
            }
        }

        return $sEnding;
    }


    /**
     * @param $arr
     */
    public static function Debug($arr) {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }

    /**
     * Помощник для дебага сущностей в окне браузера
     * @param mixed $var
     * @param bool $vardump
     * @param bool $return
     * @return string
     */
    public static function dump($var, $vardump = false, $return = false)
    {
        static $dumpCnt;

        if (is_null($dumpCnt)) {
            $dumpCnt = 0;
        }
        ob_start();

        echo '<b>DUMP #' . $dumpCnt . ':</b> ';
        echo '<p>';
        echo '<pre>';
        if ($vardump) {
            var_dump($var);
        } else {
            print_r($var);
        }
        echo '</pre>';
        echo '</p>';

        $cnt = ob_get_contents();
        ob_end_clean();
        $dumpCnt++;
        if ($return) {
            return $cnt;
        } else {
            echo $cnt;
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function getYoutubeVideoID($url) {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        return reset($matches);
    }

    public static function isXmlHttpRequest()
    {
        return @$_SERVER['HTTP_X_REQUESTED_WITH'] && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * авторизация под админом
     */
    public static function adminAuthorize() {
        global $USER;
        $USER->Authorize(1);
    }


    /**
     * Высчитывает расстояние между двумя координатами
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|int
     *
     */
    public static function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $lat1 *= M_PI / 180;
        $lat2 *= M_PI / 180;
        $lon1 *= M_PI / 180;
        $lon2 *= M_PI / 180;

        $delta = $lon1 - $lon2;

        $s_lat1 = sin($lat1);
        $s_lat2 = sin($lat2);
        $c_lat1 = cos($lat1);
        $c_lat2 = cos($lat2);
        $s_delta = sin($delta);
        $c_delta = cos($delta);

        $y = sqrt(pow($c_lat2 * $s_delta, 2) + pow($c_lat1 * $s_lat2 - $s_lat1 * $c_lat2 * $c_delta, 2));
        $x = $s_lat1 * $s_lat2 + $c_lat1 * $c_lat2 * $c_delta;

        return atan2($y, $x) * self::EARTH_RADIUS;
    }


    /**
     * @return bool
     */
    public static function IsAjax(){
        return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' );
    }




}