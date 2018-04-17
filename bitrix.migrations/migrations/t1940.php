<?
/**
 *
 * Изменения в БД:
 * - Создать инфоблок идеи
 *
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');
$bs = new CIBlockSection;
$params = Array(
    "max_len" => "100", // обрезает символьный код до 100 символов
    "change_case" => "L", // буквы преобразуются к нижнему регистру
    "replace_space" => "_", // меняем пробелы на нижнее подчеркивание
    "replace_other" => "_", // меняем левые символы на нижнее подчеркивание
    "delete_repeat_replace" => "true", // удаляем повторяющиеся нижние подчеркивания
    "use_google" => "false", // отключаем использование google
);

$arCity = array(
    "Москва",
    "Саратов",
    "Барнаул",
    "Ижевск",
    "Тольятти",
    "Санкт-Петербур",
    "Киров",
    "Владивосток",
    "Волгоград",
    "Воронеж",
    "Иркутск",
    "Екатеринбург",
    "Казань",
    "Сочи",
    "Красноярск",
    "Нижний Новгород",
    "Новосибирск",
    "Пермь",
    "Ростов-на-Дону",
    "Самара",
    "Тюмень",
    "Уфа",
    "Хабаровск",
    "Челябинск",
    "Севастополь",
    "Кемерово",
    "Оренбург",
    "Набережные Челны",
    "Томск",
    "Ульяновск",
    "Астрахань",
    "Тольяти",
    "Рязань",
    "Новокузнецк",
    "Ярославль"
);



global $DB;
$ib = 0;

$type = 'city';


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
                'NAME' => 'Города',
                'SECTION_NAME' => 'Раздел',
                'ELEMENT_NAME' => 'Элемент'
            )
        )
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
        "=CODE"=>'city'
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
        "NAME" => "Города",
        "IBLOCK_TYPE_ID" => $type,
        "SITE_ID" => $sids,
        "CODE" => "city",
        "GROUP_ID" => Array("2" => "R", "1" => "X"),
        "INDEX_ELEMENT" => "N",
        "INDEX_SECTION" => "N",
        "LIST_PAGE_URL" => '#SITE_DIR#/city/',
        'SECTION_PAGE_URL' => '#SITE_DIR#/city/#SECTION_CODE#/',
        'DETAIL_PAGE_URL' => '#SITE_DIR#/city/#SECTION_CODE#/#ELEMENT_CODE#/'
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

    foreach ($arCity as $sort => $name)
    {
        $arFields = Array(
            "ACTIVE" => 'Y',
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $iblock_shop,
            "NAME" => $name,
            "SORT" => $sort+1,
            "CODE" => CUtil::translit($name, "ru" , $params)
        );

        $ID = $bs->Add($arFields);

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

