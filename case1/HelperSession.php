<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 25.04.2017
 * Time: 21:55
 */

/**
 * Class HelperSession
 * вспомогательный класс для работы с сессиями
 */

class HelperSession
{

    public static function addToSession($value, $key)
    {
        if(isset($_SESSION[$key])){
            if(!in_array($value, $_SESSION[$key])){
                $_SESSION[$key][] = $value;
            }
        }else{
            $_SESSION[$key] = array($value);
        }
        return false;

    }

    public static function inSession($value, $key)
    {
        if(isset($_SESSION[$key])){
            if(!in_array($value, $_SESSION[$key])){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}