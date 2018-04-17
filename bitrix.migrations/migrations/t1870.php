<?
/**
 *
 * Изменения в БД:
 * - изменение иблока для статей
 *
 */
require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');

global $DB;
$IBLOCK_ID = 6; // Иблок статей

$type = 'faq';

// Массив новых полей
$arNewField = array(
    'LINK_VIDEO' => array(
        'NAME' => 'Ссылка на видео',
        'PROPERTY_TYPE' => 'E',
        'LINK_IBLOCK_ID' => 5,
    ),
    'IMAGE_GALLERY' => array(
        'NAME' => 'Изображения для слайда',
        'PROPERTY_TYPE' => 'F',
    ),
    'ITEMS_SHOP' => array(
        'NAME' => 'Товары из статьи',
        'PROPERTY_TYPE' => 'E',
        'LINK_IBLOCK_ID' => 1,
    ),
);


$rsIblockProps = CIBlock::GetProperties($IBLOCK_ID);
$arIblockProps = Array ();
while ($arIblockProp = $rsIblockProps->Fetch())
{
    $arIblockProps[strtoupper($arIblockProp['CODE'])] = Array (
        'ID'    => $arIblockProp['ID'],
        'NAME'  => $arIblockProp['NAME'],
        'CODE' => $arIblockProp['CODE'],
    );
}

foreach ($arNewField as $code => $value)
{
    $arPropData = array();
    if (!isset($arIblockProps[$code]))
    {
        $arPropData = Array (
            "NAME" => $value['NAME'],
            "ACTIVE" => "Y",
            "CODE" => $code,
            'MULTIPLE' => 'Y',
            "PROPERTY_TYPE" => $value['PROPERTY_TYPE'],
            "IBLOCK_ID" => intval($IBLOCK_ID)
        );
        if(!empty($value['ROW_COUNT']))
        {
            $arPropData['ROW_COUNT'] = $value['ROW_COUNT'];
        }
        if(!empty($value['LINK_IBLOCK_ID']))
        {
            $arPropData['LINK_IBLOCK_ID'] = $value['LINK_IBLOCK_ID'];
        }
        if(!empty($value['WITH_DESCRIPTION']))
        {
            $arPropData['WITH_DESCRIPTION'] = $value['WITH_DESCRIPTION'];
        }

        $ibProperty = new CIBlockProperty;
        $ibPropertyID = $ibProperty->Add($arPropData);
        $propSortCounter++;

    }
}

Debug($arIblockProps);