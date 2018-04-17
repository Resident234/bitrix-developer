<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Loader;

class SaleHelper
{

    /**
     * @param $cityId
     * @return string
     */
    public static function getLocationIdByCityId($cityId)
    {
        $location = '';

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'getLocationIdByCityId' . SITE_ID . $cityId;

        $cache_path = '/getLocationIdByCityId/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->GetVars();
            if (($res["location"]) && (count($res["location"]) > 0))
                $location = $res["location"];
        }

        if (empty($location)) {

            Loader::includeModule('sale');
            if ((int)$cityId) {
                $dbLoc = CSaleLocation::GetList(
                    array(),
                    array(
                        "CITY_ID" => $cityId
                    ),
                    false,
                    false,
                    array("ID", "COUNTRY_ID", "REGION_ID", "CITY_ID")
                );
                if ($arLoc = $dbLoc->Fetch()) {
                    $location = $arLoc["ID"];
                }
            }

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("location" => $location));
            }

        }

        return $location;
    }

    /**
     * @param $propCode
     * @param bool $orderID
     * @return mixed
     */
    public static function getOrderPropertyValue($propCode, $orderID = false){
        CModule::IncludeModule("sale");

        $return = "";

        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cache_time = 86400;
        $cache_id = 'getOrderPropertyValue' . SITE_ID . $propCode . $orderID;

        $cache_path = '/getOrderPropertyValue/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res["return"]) && (count($res["return"]) > 0))
                $return = $res["return"];
        }

        if (empty($return)) {

            $db_vals = CSaleOrderPropsValue::GetList(
                array("SORT" => "ASC"),
                array(
                    "ORDER_ID" => $orderID,
                    "CODE" => $propCode
                )
            );

            if ($arVals = $db_vals->Fetch()) {
                $return = $arVals["VALUE"];
            } else {
                $return = false;
            }

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            $CACHE_MANAGER->RegisterTag("return_" . md5(serialize($return)));
            $CACHE_MANAGER->EndTagCache();


            if ($cache_time > 0) {
                $cache->startDataCache();
                $cache->endDataCache(array("return" => $return));
            }
        };

        return $return;

    }





}