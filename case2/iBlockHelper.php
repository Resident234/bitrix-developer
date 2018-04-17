<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 17.04.2018
 * Time: 11:18
 */

class iBlockHelper
{
    public static function getIBlocksCodeIDMap()
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $arIBlocksIDsByCode = array();

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'getIBlocksCodeIDMap' . SITE_ID;

        $cache_path = '/getIBlocksCodeIDMap/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $res = $cache->GetVars();
            if (is_array($res["IBlocksCodeIDMap"]) && (count($res["IBlocksCodeIDMap"]) > 0))
                $arIBlocksCodeIDMap = $res["IBlocksCodeIDMap"];
        }

        if (empty($arIBlocksCodeIDMap))
        {

            $rsIBlocks = \CIBlock::GetList(
                Array(),
                Array(
                    "SITE_ID" => SITE_ID
                )
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while ($arIBlock = $rsIBlocks->Fetch()) {
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);
                $arIBlocksCodeIDMap[$arIBlock['CODE']] = $arIBlock['ID'];
            }
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->RegisterTag("getIBlocksCodeIDMap");

            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("IBlocksCodeIDMap" => $arIBlocksCodeIDMap));
            }
        }

        return $arIBlocksCodeIDMap;
    }


    public static function getIBlocksData()
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $arIBlocksData = array();

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'getIBlocksData' . SITE_ID;

        $cache_path = '/getIBlocksData/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $res = $cache->GetVars();
            if (is_array($res["IBlocksData"]) && (count($res["IBlocksData"]) > 0))
                $arIBlocksData = $res["IBlocksData"];
        }

        if (empty($arIBlocksData))
        {

            $rsIBlocks = \CIBlock::GetList(
                Array(),
                Array(
                    "SITE_ID" => SITE_ID
                )
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while ($arIBlock = $rsIBlocks->Fetch()) {
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);
                $arIBlocksData[$arIBlock['ID']] = $arIBlock;
            }
            $CACHE_MANAGER->RegisterTag("getIBlocksData");
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("IBlocksData" => $arIBlocksData));
            }
        }

        return $arIBlocksData;
    }


    public static function getIBlocksIDNameMap()
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $arIBlocksIDNameMap = array();

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'getIBlocksIDNameMap' . SITE_ID;

        $cache_path = '/getIBlocksIDNameMap/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $res = $cache->GetVars();
            if (is_array($res["IBlocksIDNameMap"]) && (count($res["IBlocksIDNameMap"]) > 0))
                $arIBlocksIDNameMap = $res["IBlocksIDNameMap"];
        }

        if (empty($arIBlocksIDNameMap))
        {

            $rsIBlocks = \CIBlock::GetList(
                Array(),
                Array(
                    "SITE_ID" => SITE_ID
                )
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while ($arIBlock = $rsIBlocks->Fetch()) {
                $CACHE_MANAGER->RegisterTag("iblock_id_".$arIBlock["ID"]);
                $arIBlocksIDNameMap[$arIBlock['ID']] = $arIBlock["NAME"];
            }
            $CACHE_MANAGER->RegisterTag("getIBlocksIDNameMap");
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("IBlocksIDNameMap" => $arIBlocksIDNameMap));
            }
        }

        return $arIBlocksIDNameMap;
    }

}


