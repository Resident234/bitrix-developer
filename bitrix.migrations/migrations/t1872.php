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

$type = 'faq';

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
                'NAME' => 'Часто задаваемые вопросы',
                'SECTION_NAME' => 'Раздел',
                'ELEMENT_NAME' => 'Элемент'
            )
        ),
        "LIST_PAGE_URL" => '#SITE_DIR#/podderzhka/faq/',
        'SECTION_PAGE_URL' => '',
        'DETAIL_PAGE_URL' => '#SITE_DIR#/podderzhka/faq/#ELEMENT_CODE#/'
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
        "=CODE"=>'faq'
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
        "NAME" => "Часто задаваемые вопросы",
        "IBLOCK_TYPE_ID" => $type,
        "SITE_ID" => $sids,
        "CODE" => "faq",
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
            "NAME" => "Email",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "EMAIL",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "Имя",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "NAME",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
        );

        $arProps[] = Array(
            "NAME" => "Текст вопроса",
            "ACTIVE" => "Y",
            "SORT" => "100",
            "CODE" => "PREVIEW_TEXT",
            "PROPERTY_TYPE" => "S",
            "IBLOCK_ID" => $ib,
            "WITH_DESCRIPTION" =>"N",
            "ROW_COUNT" => "5",
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