<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */


class UserHelper
{
    /**
     * @param $ip
     * @return array|mixed
     */
    public static function getUserInfoByIp($ip) {
        $result = [];
        if($ip) {
            $ch = curl_init('http://api.sypexgeo.net/json/'.$ip);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $data = curl_exec($ch);

            if (!curl_errno($ch)) {
                $result = json_decode($data, true);
            }
            curl_close($ch);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public static function isBot() {
        $is_bot = preg_match(
            "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i",
            $_SERVER['HTTP_USER_AGENT']
        );

        return $is_bot ? true: false;
    }



}