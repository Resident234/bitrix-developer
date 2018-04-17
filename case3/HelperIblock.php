<?php

class HelperIblock
{
    public static function getLikedNews($userID, $IBlockNewsID)
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $arLikedNewsIDs = array();

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'getLikedNews_' . SITE_ID . "_" . $userID . "_" . $IBlockNewsID;
        $cache_path = '/getLikedNews/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["arLikedNewsIDs"]) &&
                (count($res["arLikedNewsIDs"]) > 0)) {
                echo "cached";
                $arLikedNewsIDs = $res["arLikedNewsIDs"];
            }
        }


        if (empty($arLikedNewsIDs)) {

            $arFilter = array(
                "IBLOCK_ID" => $IBlockNewsID,
                "PROPERTY_LIKED_USERS" => $userID
            );
            $rsElement = CIBlockElement::GetList(
                array(),
                $arFilter,
                false,
                false,
                array("ID", "PROPERTY_LIKED_USERS")
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while ($arElement = $rsElement->GetNext()) {
                $arLikedNewsIDs[] = $arElement["ID"];
                $CACHE_MANAGER->RegisterTag("iblock_id_" . $arElement["ID"]);
            }
            $CACHE_MANAGER->RegisterTag("LikedNew");
            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("arLikedNewsIDs" => $arLikedNewsIDs));
            }

        }

        return $arLikedNewsIDs;

    }

}