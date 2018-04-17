<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

if (!CModule::IncludeModule('iblock')) {
    ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}

class IBlockHelper
{
    /**
     * Полностью очищает инфоблок
     *
     * @param $iblockID
     */
    public static function clear($iblockID) {

        $arSelect = Array('ID');
        $arFilter = Array('IBLOCK_ID'=>IntVal($iblockID));
        $oElement = new CIBlockElement;
        $oSection = new CIBlockSection;

        $dbElements = $oElement->GetList(Array(), $arFilter, false, false, $arSelect);

        while($arEl = $dbElements->Fetch())
        {
            $oElement->Delete($arEl['ID']);
        }

        $dbSections = $oSection->GetList(Array(), $arFilter, false, $arSelect, false);

        while($arSect = $dbSections->Fetch())
        {
            $oSection->Delete($arSect['ID'],false);
        }

        return;

    }


    /**
     * @param $iblock
     * @return bool
     */
    function randomSortAtIblockElems($iblock){
        if (!$iblock){
            return false;
        }
        if (!CModule::IncludeModule("iblock")){
            return false;
        }
        $res = CIBlockElement::GetList(
            Array(),
            Array(
                "IBLOCK_ID" => $iblock,
                "ACTIVE"    => "Y",
                ">SORT"     => "99",
            ),
            false,
            false,
            Array(
                'ID'
            )
        );

        $count = $res->SelectedRowsCount();
        $min = 100;
        $max = $count * 100;

        while ($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();

            $newSort = rand($min, $max);
            $el = new CIBlockElement;
            $arLoadProductArray = Array(
                "SORT" => $newSort
            );
            $el->Update($arFields["ID"], $arLoadProductArray);
        }

        return true;
    }

    /**
     * не пустая ли секция
     * @param $sectionID
     * @param int $iblockID
     * @return bool
     */
    public static function sectionHasItems($sectionID, $iblockID = 0) {

        CModule::IncludeModule('iblock');

        $hasItems = false;

        $arFilter = array("SECTION_ID"=>$sectionID, '>PROPERTY_MINIMUM_PRICE' => 0, 'ACTIVE' => 'Y');
        if($iblockID) {
            $arFilter["IBLOCK_ID"] = $iblockID;
        }
        $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, ['ID', 'PROPERTY_MINIMUM_PRICE']);

        if($arItem = $rsElements->Fetch()) {
            $hasItems = true;
        }

        return $hasItems;
    }

}