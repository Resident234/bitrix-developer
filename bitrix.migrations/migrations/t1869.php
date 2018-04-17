<?
/**
 *
 * Изменения в БД:
 * - Создать инфоблок идеи
 *
 */
require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');

global $DB;
$ib = 0;

$type = 'ideas';

$sids = array();
$arQuery = CSite::GetList($sort = "sort", $order = "desc", Array());
while ($res = $arQuery->Fetch())
{
    $sids[] = $res["ID"];
}

$arTypes = CIBlockType::GetByID($type);
if (!$arType = $arTypes->Fetch())
{
    $arFields = Array(
        'ID' => $type,
        'SECTIONS' => 'Y',
        'IN_RSS' => 'N',
        'SORT' => 100,
        'LANG' => Array(
            'ru' => Array(
                'NAME' => 'Идеи',
                'SECTION_NAME' => 'Раздел',
                'ELEMENT_NAME' => 'Элемент'
            )
        ),
        "LIST_PAGE_URL" => '#SITE_DIR#/ideas/',
        'SECTION_PAGE_URL' => '#SITE_DIR#/ideas/#SECTION_CODE#/',
        'DETAIL_PAGE_URL' => '#SITE_DIR#/ideas/#SECTION_CODE#/#ELEMENT_CODE#/'
    );
    $obBlocktype = new CIBlockType;
    $DB->StartTransaction();
    $res = $obBlocktype->Add($arFields);
    if (!$res)
    {
        $DB->Rollback();
        echo 'Error: '.$obBlocktype->LAST_ERROR.'<br />';
        die("TYPE_ERROR");
    }
    else
        $DB->Commit();
}

$res = CIBlock::GetList(
    Array(),
    Array(
        'TYPE'=>$type,
        'SITE_ID'=>$sids,
        'ACTIVE'=>'Y',
        "CNT_ACTIVE"=>"Y",
        "=CODE"=>'ideas'
    ), true
);
while($ar_res = $res->Fetch())
{
    $ib = $ar_res['ID'];
}


if($ib == 0)
{
    $arFields = Array(
        "ACTIVE" => "Y",
        "NAME" => "Идеи",
        "IBLOCK_TYPE_ID" => $type,
        "SITE_ID" => $sids,
        "CODE" => "ideas",
        "GROUP_ID" => Array("2" => "R", "1" => "X"),
        "INDEX_ELEMENT" => "Y",
        "INDEX_SECTION" => "Y"
    );
    $iblockB = new CIBlock();
    if ($iblock_shop = $iblockB->Add($arFields))
    {
        CheckIBlock($iblock_shop);
    }
    else
    {
        echo 'Error: '.$ib->LAST_ERROR.'<br />';
    }
}




function CheckIBlock($ib)
{
    if (!CModule::IncludeModule("iblock")) die();
    $r = true;

    $iblock = CIBlock::GetByID($ib)->GetNext();
    if (!$iblock)
        $r = false;

    if ($r) {
        $arPropsIB = array();
        $res = CIBlock::GetProperties($ib, Array(), Array());
        while ($res_arr = $res->Fetch())
            $arPropsIB[] = $res_arr["CODE"];

        $arProps[] = Array(
            "NAME" => "Товары",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "ITEMS_SHOP",
            "PROPERTY_TYPE" => "E",
            "IBLOCK_ID" => $ib,
            'MULTIPLE' => 'Y',
            'LINK_IBLOCK_ID' => 1,
        );


        $iblockproperty = new CIBlockProperty;
        foreach ($arProps as $pr)
        {
            if (!in_array($pr["CODE"], $arPropsIB))
                $PropertyID = $iblockproperty->Add($pr);
        }
    }
    return $r;
}