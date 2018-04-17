<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */



class ArrayHelper
{

    /**
     * @param $array
     * @return stdClass
     */
    function array_to_object($array) {
        $obj = new stdClass;
        foreach($array as $k => $v) {
            if(strlen($k)) {
                if(is_array($v)) {
                    $obj->{$k} = array_to_object($v);
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }

    /**
     * Сортирует вложенные массивы в многомерном массиве
     * по определенному значению ключа вложенных массивов
     *
     * @param $array
     * @param $value
     * @param bool $asc
     * @param bool $preserveKeys
     * @return mixed
     */
    public static function sortBySubValue(
        $array,
        $value,
        $asc = true,
        $preserveKeys = false
    ) {
        if (is_object(reset($array))) {
            $preserveKeys ? uasort($array,
                function ($a, $b) use ($value, $asc) {
                    return $a->{$value} == $b->{$value} ? 0 : ($a->{$value} - $b->{$value}) * ($asc ? 1 : -1);
                }) : usort($array, function ($a, $b) use ($value, $asc) {
                return $a->{$value} == $b->{$value} ? 0 : ($a->{$value} - $b->{$value}) * ($asc ? 1 : -1);
            });
        } else {
            $preserveKeys ? uasort($array,
                function ($a, $b) use ($value, $asc) {
                    return $a[$value] == $b[$value] ? 0 : ($a[$value] - $b[$value]) * ($asc ? 1 : -1);
                }) : usort($array, function ($a, $b) use ($value, $asc) {
                return $a[$value] == $b[$value] ? 0 : ($a[$value] - $b[$value]) * ($asc ? 1 : -1);
            });
        }
        return $array;
    }

    /**
     * генерация массива случайных значений, в котром повторяющиеся элементы не
     * расположены друг за другом
     * @param $start
     * @param $end
     * @param $count
     * @return array
     */
    public static function generate_rand_massive($start, $end, $count)
    {

        $M = array();

        $M[0] = mt_rand($start, $end);
        for ($i = 1; $i < $count; $i++) {
            while (1) {
                $int = mt_rand($start, $end);
                if ($M[$i - 1] != $int) {
                    $M[$i] = $int;
                    break;
                }
            }
        }

        return $M;

    }


    /**
     * генерация массива перестановок
     * @param $start
     * @param $end
     * @param $count
     * @return array
     * TODO: возможно будет дописан или переписан
     *
     */
    public static function generate_all_permutation($start, $end, $count)
    {

        $tmp_number = ($end - $start) + 1;
        $M = array();
        for ($i = $start; $i <= $end; $i++) {
            $M[] = $i;
        }
        shuffle($M);

        $M_tmp = $M;

        for ($i = 0; $i < ceil($count / $tmp_number); $i++) {
            array_unshift($M_tmp, array_pop($M_tmp));
            $M = array_merge($M, $M_tmp);
        }

        return $M;
    }


    /**
     * генерация массива перестановок с учётом кол-ва элементов в строке
     * @param $start
     * @param $end
     * @param $count
     * @param $row - количество элементов в строке
     * @return array
     *
     * пример:
     * ArrayHelper::generate_all_permutation_custom(1, 4, 10, 3)
     *
     *
     * Array
     * (
     * [0] => 3
     * [1] => 2
     * [2] => 1
     * [3] => 4
     * [4] => 3
     * [5] => 2
     * [6] => 1
     * [7] => 4
     * [8] => 3
     * [9] => 2
     * [10] => 1
     * [11] => 4
     * )
     */
    public static function generate_all_permutation_custom($start, $end, $count, $row)
    {
        $tmp_number2 = ($end - $start) + 1;
        $tmp_number = $row;


        $M = array();
        $M2 = array();

        for ($i = $start; $i <= $end; $i++) {
            $M2[] = $i;
        }
        shuffle($M2);

        $M_tmp = $M2;

        for ($i = 0; $i < ceil($count / $tmp_number); $i++) {

            $M_tmp2 = $M_tmp;
            $diff = $tmp_number2 - $tmp_number;
            for ($i2 = count($M_tmp2) - 1, $i1 = 0; $i2 >= 0, $i1 < $diff; $i2--, $i1++) {
                unset($M_tmp2[$i2]);
            }


            array_unshift($M_tmp, array_pop($M_tmp));
            //array_unshift($M_tmp2, array_pop($M_tmp2));
            $M = array_merge($M, $M_tmp2);
        }


        return $M;

    }


    /**
     *
     * @param $array
     * @param $cols
     * @return array
     *
     * $arr1 = array(
     * array('id'=>1,'name'=>'aA','cat'=>'cc'),
     * array('id'=>2,'name'=>'aa','cat'=>'dd'),
     * array('id'=>3,'name'=>'bb','cat'=>'cc'),
     * array('id'=>4,'name'=>'bb','cat'=>'dd')
     * );
     *
     * $arr2 = array_msort($arr1, array('name'=>SORT_DESC, 'cat'=>SORT_ASC));
     *
     *
     * arr1:
     * 0:
     * id: 1 (int)
     * name: aA (string:2)
     * cat: cc (string:2)
     * 1:
     * id: 2 (int)
     * name: aa (string:2)
     * cat: dd (string:2)
     * 2:
     * id: 3 (int)
     * name: bb (string:2)
     * cat: cc (string:2)
     * 3:
     * id: 4 (int)
     * name: bb (string:2)
     * cat: dd (string:2)
     *
     *
     *
     * arr2:
     * 2:
     * id: 3 (int)
     * name: bb (string:2)
     * cat: cc (string:2)
     * 3:
     * id: 4 (int)
     * name: bb (string:2)
     * cat: dd (string:2)
     * 0:
     * id: 1 (int)
     * name: aA (string:2)
     * cat: cc (string:2)
     * 1:
     * id: 2 (int)
     * name: aa (string:2)
     * cat: dd (string:2)
     *
     *
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }


    /**
     * @return mixed
     * сортировка многомерного массива
     *
     * $arResult["TOP_ITEMS"] = ArrayHelper::arrayOrderBy($arResult["TOP_ITEMS"], "SORT", SORT_ASC);
     * $arResult["BOTTOM_ITEMS"] = ArrayHelper::arrayOrderBy($arResult["BOTTOM_ITEMS"], "SORT", SORT_ASC);
     */
    public static function arrayOrderBy()
    {

        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);


    }


    /**
     * парсинг временной таблицы
     *
     * пример:
     * вход: {"0":[["10:00","20:00"]],"1":[["10:00","20:00"]],"2":[["10:00","20:00"]],"3":[["10:00","20:00"]],"4":[["10:00","20:00"]],"5":[["10:00","20:00"]],"6":[["10:00","20:00"]]}
     * выход:
     * Array
     * (
     * [0] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [1] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [2] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [3] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [4] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [5] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * [6] => Array
     * (
     * [startWorkTime] => 10:00
     * [endWorkTime] => 20:00
     * )
     *
     * )
     * @param $TIMETABLE
     * @return array
     */
    public static function parseTimeTable($TIMETABLE){

        if ($objTimeTable = json_decode($TIMETABLE)) {

            for($weekday = 0; $weekday < 7; $weekday++){
                foreach($objTimeTable->$weekday as $arTimeInterval) {

                    $arTimeTableResult[] = array(
                        "startWorkTime" => $arTimeInterval[0],
                        "endWorkTime" => $arTimeInterval[1]
                    );

                }
            }


        } else {
            //в некоторых случаях json_decode у меня не срабатывал, поэтому я написал
            //костыльный запасной ввариант

            $TIMETABLE = trim($TIMETABLE, "{");
            $TIMETABLE = trim($TIMETABLE, "}");
            $arTimeTable = explode("],", $TIMETABLE);

            $arTimeTableResult = array();
            foreach ($arTimeTable as $keyItem => &$item) {
                preg_match('/\[(.+)\]/', $item, $out);
                $item = $out[1];
                $item = trim($item, "]");
                $item = trim($item, "[");
                $arItem = explode(",", $item);

                $arTimeTableResult[$keyItem]["startWorkTime"] = trim($arItem[0], '&quot;');
                $arTimeTableResult[$keyItem]["endWorkTime"] = trim($arItem[1], '&quot;');

                $arTimeTableResult[$keyItem]["startWorkTime"] = trim($arItem[0], '"');
                $arTimeTableResult[$keyItem]["endWorkTime"] = trim($arItem[1], '"');

            }

        };


        return ($arTimeTableResult);



    }


    /**
     * Поиск по значению ключа в многомерном массиве
     * @param $array
     * @param $key
     * @param $value
     * @return array
     */

    public static function searchInMultiArrayByKeyValue($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value)
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge($results, self::searchInMultiArrayByKeyValue($subarray, $key, $value));
        }

        return $results;
    }


    /**
     * @param array $arr
     * @return array
     */
    public static function arrayToString(array $arr)
    {
        foreach ($arr as &$a) {
            $a = (string)$a;
        }
        return $arr;
    }

    /**
     * @param array $arr
     * @return array
     */
    public static function arrayToInt(array $arr)
    {
        foreach ($arr as &$a) {
            $a = (int)$a;
        }
        return $arr;
    }

    /**
     *  удалить элемент по ключу из многомерного массива
     * @param $array
     * @param $keyParameter
     */
    public static function recursiveRemovalByKey(&$array, $keyParameter)
    {
        if (is_array($array)) {
            foreach ($array as $key => &$arrayElement) {
                if (is_array($arrayElement)) {
                    self::recursiveRemovalByKey($arrayElement, $keyParameter);
                } else {
                    if ($key === $keyParameter) {
                        unset($array[$key]);
                    }
                }
            }
        }
    }


    /**
     *  удалить элемент из многомерного массива
     * @param $array
     * @param $val
     */
    public static function recursiveRemoval(&$array, $val)
    {
        if (is_array($array)) {
            foreach ($array as $key => &$arrayElement) {
                if (is_array($arrayElement)) {
                    self::recursiveRemoval($arrayElement, $val);
                } else {
                    if ($arrayElement == $val) {
                        unset($array[$key]);
                    }
                }
            }
        }
    }


    /**
     * удаление из массива элемента в определённым ключом
     * @param $keyElementToRemove
     * @param $array
     */
    public static function removeElementByKey($keyElementToRemove, &$array) {
        if ($array[$keyElementToRemove])
        {
            unset($array[$keyElementToRemove]);
        }
    }


    /**
     * среднее арифметическое массива
     * @param $arCoordinates
     * @return float|int
     */
    public static function arithmeticalMean($arCoordinates) {
        $count = count($arCoordinates);
        $sum = array_sum($arCoordinates);
        $am = $sum/$count;

        return $am;
    }


    /**
     *
     * @param $vector
     * @return array
     *
     * in:
     * Array
     * (
     * [name] => Array
     * (
     * [0] => replay_pid8252.log
     * [1] => replay_pid12232.log
     * [2] => 4cf1c164b0746d7af4ad6193ecd79aa3.jpg
     * [3] => 742a752c9c16.jpg
     * [4] => 742a752c9c16.jpg
     * )
     *
     * [type] => Array
     * (
     * [0] => application/octet-stream
     * [1] => application/octet-stream
     * [2] => image/jpeg
     * [3] => image/jpeg
     * [4] => image/jpeg
     * )
     *
     * [tmp_name] => Array
     * (
     * [0] => /home/gs/tmp/phpIQZWiH
     * [1] => /home/gs/tmp/phpjTkHJi
     * [2] => /home/gs/tmp/phpcCWTaU
     * [3] => /home/gs/tmp/phpdvScCv
     * [4] => /home/gs/tmp/phpQhvy36
     * )
     *
     * [error] => Array
     * (
     * [0] => 0
     * [1] => 0
     * [2] => 0
     * [3] => 0
     * [4] => 0
     * )
     *
     * [size] => Array
     * (
     * [0] => 1292482
     * [1] => 1123358
     * [2] => 274618
     * [3] => 114044
     * [4] => 114044
     * )
     *
     * )
     *
     *
     *
     *
     * out:
     * Array
     * (
     * [0] => Array
     * (
     * [name] => replay_pid8252.log
     * [type] => application/octet-stream
     * [tmp_name] => /home/gs/tmp/phpIQZWiH
     * [error] => 0
     * [size] => 1292482
     * )
     *
     * [1] => Array
     * (
     * [name] => replay_pid12232.log
     * [type] => application/octet-stream
     * [tmp_name] => /home/gs/tmp/phpjTkHJi
     * [error] => 0
     * [size] => 1123358
     * )
     *
     * [2] => Array
     * (
     * [name] => 4cf1c164b0746d7af4ad6193ecd79aa3.jpg
     * [type] => image/jpeg
     * [tmp_name] => /home/gs/tmp/phpcCWTaU
     * [error] => 0
     * [size] => 274618
     * )
     *
     * [3] => Array
     * (
     * [name] => 742a752c9c16.jpg
     * [type] => image/jpeg
     * [tmp_name] => /home/gs/tmp/phpdvScCv
     * [error] => 0
     * [size] => 114044
     * )
     *
     * )
     *
     *
     */
    public static function diverseArray($vector)
    {
        $result = array();

        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cache_time = 86400;
        $cache_id = 'diverseArray' . SITE_ID;

        $cache_path = '/diverseArray/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res["result"]) && (count($res["result"]) > 0))
                $result = $res["result"];
        }

        if (empty($result)) {


            foreach ($vector as $key1 => $value1) {
                foreach ($value1 as $key2 => $value2) {
                    $result[$key2][$key1] = $value2;
                }
            }
            $names = array();
            $result = array_filter($result, function ($val) use (&$names) {
                if (!in_array($val['name'], $names)) {
                    $names[] = $val['name'];
                    return true;
                }
                return false;
            });

            if ($cache_time > 0) {
                $cache->startDataCache();
                $cache->endDataCache(array("result" => $result));
            }
        }

        return $result;
    }


    /**
     * @param array $input
     * @param $columnKey
     * @param null $indexKey
     * @return array|bool
     */
    public static function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }



}