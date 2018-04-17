<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Loader;

class CatalogHelper
{

    /**
     * Приводит товары в нужное состоние активен/неактивек
     * @param $IBLOCK_ID
     */
    public static function normalizeActiveCatalogState($IBLOCK_ID){

        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "ACTIVE");
        $arFilter = Array("IBLOCK_ID" => IntVal($IBLOCK_ID), "CATALOG_QUANTITY" => "0", "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $el = new CIBlockElement;
            $el->Update($arFields["ID"], Array("ACTIVE" => "N"));
        }

        $arFilter = Array("IBLOCK_ID" => IntVal($IBLOCK_ID), "!CATALOG_QUANTITY" => "0", "ACTIVE" => "N");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $el = new CIBlockElement;
            $el->Update($arFields["ID"], Array("ACTIVE" => "Y"));
        }

    }

}