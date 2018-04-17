<?
/**
 *
 * Изменения в БД:
 * - Создать инфоблок магазинов
 *
 */
require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');

global $DB;
$ib = 0;

$type = 'shops';

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
                'NAME' => 'Магазины',
                'SECTION_NAME' => 'Раздел',
                'ELEMENT_NAME' => 'Магазины'
            )
        ),
        "LIST_PAGE_URL" => '#SITE_DIR#/dealers/',
        'SECTION_PAGE_URL' => '',
        'DETAIL_PAGE_URL' => '#SITE_DIR#/dealers/#ELEMENT_CODE#/'
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
        "=CODE"=>'shops-info'
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
        "NAME" => "Магазины",
        "IBLOCK_TYPE_ID" => $type,
        "SITE_ID" => $sids,
        "CODE" => "shops-info",
        "GROUP_ID" => Array("2" => "R", "1" => "X"),
        "INDEX_ELEMENT" => "N",
        "INDEX_SECTION" => "N"
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
            "NAME" => "Метро",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "METRO",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "Время работы",
            "ACTIVE" => "Y",
            "SORT" => "200",
            "CODE" => "TIME_WORK",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "Адрес",
            "ACTIVE" => "Y",
            "SORT" => "300",
            "CODE" => "ADRES",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "Телефон",
            "ACTIVE" => "Y",
            "SORT" => "400",
            "CODE" => "PHONE",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "На машине",
            "ACTIVE" => "Y",
            "SORT" => "500",
            "CODE" => "ON_CAR",
            "PROPERTY_TYPE" => "S",
            "ROW_COUNT" => "5",
            "IBLOCK_ID" => $ib,
        );

        $arProps[] = Array(
            "NAME" => "На общественном транспорте",
            "ACTIVE" => "Y",
            "SORT" => "600",
            "CODE" => "ON_TRANSPORT",
            "PROPERTY_TYPE" => "S",
            "ROW_COUNT" => "5",
            "IBLOCK_ID" => $ib,
        );

        $arProps[] = Array(
            "NAME" => "Карта",
            "ACTIVE" => "Y",
            "SORT" => "700",
            "CODE" => "YANDEX",
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "map_yandex",
            "IBLOCK_ID" => $ib,
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