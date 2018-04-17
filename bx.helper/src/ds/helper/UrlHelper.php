<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */


class UrlHelper
{
    /**
     * @param $key
     * @return string
     */
    public static function removeKeyFromURLString($key)
    {
        parse_str($_SERVER['QUERY_STRING'], $vars);
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        //  $url = $protocol . strtok($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?') . http_build_query(array_diff_key($vars,array($key=>"")));
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?' . http_build_query(array_diff_key($vars, array($key => "")));
        return $url;

    }


    /**
     * удаление параметров из текущего URL
     * @param $M - массив параметров, которые нужно удалить
     * @return string
     */
    public static function removeKeyFromCurrentURL($M) {

        $arKeys = array();
        foreach($M as $key){
            $arKeys[$key] = "";
        }

        parse_str($_SERVER['QUERY_STRING'], $vars);
        //echo "==".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']."==";

        $url = strtok($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?') . "?" . http_build_query(array_diff_key($vars,$arKeys));

        $protocol = (CMain::IsHTTPS()) ? "https://" : "http://";

        return $protocol.$url;
    }


    /**
     * удаление параметров из URL
     * @param $url
     * @param $M
     * @return string
     */
    public static function removeKeyFromURL($url, $M) {

        $arKeys = array();
        foreach($M as $key){
            $arKeys[$key] = "";
        }
        $result=parse_url($url);

        $QUERY_STRING = $result['query'];

        parse_str($QUERY_STRING, $vars);
        if(!empty(http_build_query(array_diff_key($vars,$arKeys)))){
            $url = strtok($_SERVER['HTTP_HOST'] . $result['path'], '?') . "?" . http_build_query(array_diff_key($vars,$arKeys));
        }else{
            $url = strtok($_SERVER['HTTP_HOST'] . $result['path'], '?');
        }
        $protocol = (CMain::IsHTTPS()) ? "https://" : "http://";

        return $protocol.$url;
    }

    /**
     * @param $a_data
     * @param bool $url
     * @return string
     *
     * $url = 'http://z-site.ru/?my_param=hello&my_param_2=bye';
     * echo  zAddUrlGet(array('my_param_2'=>'goodbye','new_param'=>'this is new param'),$url);
     * // http://z-site.ru/?my_param=hello&my_param_2=goodbye&new_param=this+is+new+param
     *
     * $url = 'http://z-site.ru/';
     * echo  zAddUrlGet(array('my_param_2'=>'goodbye','new_param'=>'this is new param'),$url);
     * // http://z-site.ru/?my_param_2=goodbye&new_param=this+is+new+param
     */
    public static function zAddUrlGet($a_data, $url = false)
    {
        $http = $_SERVER['HTTPS'] ? 'https' : 'http';


        if ($url === false) {
            $url = $http . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }
        $query_str = parse_url($url);
        $path = !empty($query_str['path']) ? $query_str['path'] : '';
        $return_url = $query_str['scheme'] . '://' . $query_str['host'] . $path;
        $query_str = !empty($query_str['query']) ? $query_str['query'] : false;
        $a_query = array();
        if ($query_str) {
            parse_str($query_str, $a_query);
        }
        $a_query = array_merge($a_query, $a_data);
        $s_query = http_build_query($a_query);
        if ($s_query) {
            $s_query = '?' . $s_query;
        }
        return $return_url . $s_query;
    }

    /**
     * @return string
     */
    public static function selfURL()
    {
        if (!isset($_SERVER['REQUEST_URI'])) $suri = $_SERVER['PHP_SELF'];
        else $suri = $_SERVER['REQUEST_URI'];
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
        $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
        $pr = substr($sp, 0, strpos($sp, "/")) . $s;
        $pt = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);
        return $pr . "://" . $_SERVER['SERVER_NAME'] . $pt . $suri;
    }

}