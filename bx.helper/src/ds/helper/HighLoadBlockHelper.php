<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

/**
 * Class HelperHighLoadBlock
 * вспомогательный класс дл€ работы с Highloadblock
 */
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

CModule::IncludeModule('highloadblock');

class HighLoadBlockHelper
{
    /**
     * получение экземпл€ра класса
     * @param HlBlockId $
     * @return \Bitrix\Main\Entity\DataManager|bool
     */
    public static function GetEntityDataClass($HlBlockId)
    {

        if (empty($HlBlockId) || $HlBlockId < 1)
        {
            return false;
        }



        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;

    }

    /**
     * получение данных из HL блока по XML_ID элемнта
     * @param $HLiB_name - название таблицы, например b_hlbd_razmermatrasovshirina
     * @param $XML_ID - внешний код элемента, например bDkMMuzT
     * @return mixed
     */
    public static function GetElementByXMLID($HLiB_name, $XML_ID)
    {

        $HLinfo = "";

        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cache_time = 86400;
        $cache_id = 'GetElementByXMLID' . SITE_ID . $HLiB_name . $XML_ID;

        $cache_path = '/GetElementByXMLID/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res["HLinfo"]) && (count($res["HLinfo"]) > 0))
                $result = $res["HLinfo"];
        }

        if (empty($result)) {

            $rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('TABLE_NAME' => $HLiB_name)));
            if ($hldata = $rsData->fetch()) {
                $hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
                $hlDataClass = $hldata['NAME'] . 'Table';
                $res = $hlDataClass::getList(array(
                        'filter' => array(
                            'UF_XML_ID' => $XML_ID,
                        ),
                        'select' => array("*"),
                        'order' => array(
                            'UF_NAME' => 'asc'
                        ),
                    )
                );
                global $CACHE_MANAGER;
                $CACHE_MANAGER->StartTagCache($cache_path);

                if ($row = $res->fetch()) {

                    $CACHE_MANAGER->RegisterTag("row_" . md5(serialize($row)));
                    $HLinfo = $row;

                }

                $CACHE_MANAGER->EndTagCache();

            }

            if ($cache_time > 0) {
                $cache->startDataCache();
                $cache->endDataCache(array("HLinfo" => $HLinfo));
            }
        }

        return $HLinfo;

    }

}