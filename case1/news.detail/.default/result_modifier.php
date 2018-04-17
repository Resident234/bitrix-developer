<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


$arItemIDs = array();
if($arResult['PROPERTIES']['PINS']['VALUE']) {
    $rsPins = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arResult['PROPERTIES']['PINS']['LINK_IBLOCK_ID'], 'SECTION_ID' => $arResult['PROPERTIES']['PINS']['VALUE'], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', 'INCLUDE_SUBSECTIONS'=>'Y'), false, false, array('ID', 'IBLOCK_SECTION_ID', 'PROPERTY_COORDS', 'PROPERTY_ITEM'));
    while ($arPin = $rsPins->Fetch()) {
        if($arPin['PROPERTY_ITEM_VALUE']){
        $arItemIDs[$arPin['PROPERTY_ITEM_VALUE']] = $arPin['PROPERTY_ITEM_VALUE'];
        }
        $arResult['PINS'][] = $arPin;
    }
}


if($arResult['PROPERTIES']['PINS']['VALUE']) {
    $rsSlides = CIBlockSection::GetList(array('sort'=>'asc'), array('IBLOCK_ID'=>$arResult['PROPERTIES']['PINS']['LINK_IBLOCK_ID'], 'SECTION_ID'=>$arResult['PROPERTIES']['PINS']['VALUE'], 'ACTIVE'=>'Y'), false, array('ID', 'PICTURE'));
    while ($arSlide = $rsSlides->Fetch()) {
        $arSlides[$arSlide['ID']] = $arSlide;
    }

    if(!empty($arSlides)) {
        $rsPins = CIBlockElement::GetList(array(), array('IBLOCK_ID'=>$arResult['PROPERTIES']['PINS']['LINK_IBLOCK_ID'], 'SECTION_ID'=>array_keys($arSlides), 'ACTIVE'=>'Y', 'GLOBAL_ACTIVE'=>'Y'), false, false, array('ID', 'IBLOCK_SECTION_ID', 'NAME', 'PROPERTY_COORDS', 'PROPERTY_ITEM'));
        while($arPin = $rsPins->Fetch()) {
            if($arPin['PROPERTY_ITEM_VALUE']) {
                $arItemIDs[$arPin['PROPERTY_ITEM_VALUE']] = $arPin['PROPERTY_ITEM_VALUE'];
            }
            $arSlides[$arPin['IBLOCK_SECTION_ID']]['PINS'][$arPin['ID']] = $arPin;
        }

        $arResult['SLIDER_PINS'] = $arSlides;

        if(!empty($arItemIDs)) {
            $arSelect = array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'DETAIL_PICTURE',
                'DETAIL_PAGE_URL',
                'PROPERTY_ARTNUMBER',
                'CATALOG_GROUP_'.$_SESSION['PRICE_ZONE'],
            );
            $rsItems = CIBlockElement::GetList(array(), array('IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ID' => $arItemIDs), false, false, $arSelect);
            while ($arItem = $rsItems->GetNext()) {
                $arItemPrices = CIBlockPriceTools::GetItemPrices(IBLOCK_CATALOG_ID, $arResult['PRICES'], $arItem);
                $arItem['PRICE'] = $arItemPrices;

                if(empty($arItem['DETAIL_PICTURE'])) {
                    $arPhoto = CIBlockElement::GetProperty($arItem['IBLOCK_ID'], $arItem['ID'], 'sort', 'asc', array('CODE' => 'MORE_PHOTO'))->Fetch();
                    $arItem['DETAIL_PICTURE'] = $arPhoto['VALUE'];
                }

                $arItem['ADD_URL'] = $APPLICATION->GetCurPage().'?action=ADD2BASKET&id='.$arItem['ID'];

                $arResult['PIN_ITEMS'][$arItem['ID']] = $arItem;
            }
        }
    }
}
if($arResult['PROPERTIES']['BENEFITS']['VALUE']) {
    $arFilter = array(
        'IBLOCK_ID' => $arResult['PROPERTIES']['BENEFITS']['LINK_IBLOCK_ID'],
        'SECTION_ID' => $arResult['PROPERTIES']['BENEFITS']['VALUE'],
        'ACTIVE' => 'Y',
        'GLOBAL_ACTIVE' => 'Y'
    );
    $arSelect = array(
        'ID',
        'NAME',
        'PROPERTY_BENEFITS',
        'PROPERTY_SUBTITLE',
        'PROPERTY_ITEM',
        'PREVIEW_PICTURE',
    );
    $rsBenefits = CIBlockElement::GetList(array("SORT" => "ASC"), $arFilter,
        false, false, $arSelect);
    while ($arBenefit = $rsBenefits->Fetch()) {
    	if($arBenefit['PROPERTY_ITEM_VALUE']){
        $arItemIDs[$arBenefit['PROPERTY_ITEM_VALUE']] = $arBenefit['PROPERTY_ITEM_VALUE'];
	    }
        $img = CFile::ResizeImageGet($arBenefit['PREVIEW_PICTURE'], array('width'=>480, 'height'=>480));
        $arBenefit['PREVIEW_PICTURE'] = $img['src'];
        $arResult['BENEFITS'][] = $arBenefit;
    }
}

if (!empty($arItemIDs)) {
    $arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices(IBLOCK_CATALOG_ID, array($arParams["PRICE_CODE"]));
    $arSelect = array(
        'ID',
        'IBLOCK_ID',
        'NAME',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PAGE_URL',
        'PROPERTY_ARTNUMBER',
        'CATALOG_GROUP_'.$_SESSION['PRICE_ZONE'],
        'PROPERTY_OLD_PRICE_'.$arParams['PRICE_CODE'],
    );
    $rsItems = CIBlockElement::GetList(array(), array('IBLOCK_ID' => IBLOCK_CATALOG_ID, 'ID' => $arItemIDs), false, false, $arSelect);
    while ($arItem = $rsItems->GetNext()) {
        $arItemPrices = CIBlockPriceTools::GetItemPrices(IBLOCK_CATALOG_ID, $arResult['PRICES'], $arItem);
        $arItem['PRICE'] = $arItemPrices;

        $arItem['OLD_PRICE'] = $arItem['PROPERTY_OLD_PRICE_'.$_SESSION['PRICE_CODE'].'_VALUE'];
        if($arItem['OLD_PRICE']) {
            $arItem['DISCOUNT'] = round(100 - $arItem['PRICE'][$arParams['PRICE_CODE']]['DISCOUNT_VALUE'] / ($arItem['OLD_PRICE'] / 100));
        }

        $arItem['ADD_URL'] = $APPLICATION->GetCurPage().'?action=ADD2BASKET&id='.$arItem['ID'];

        $arResult['ITEMS'][$arItem['ID']] = $arItem;
    }
}

if($arResult['PROPERTIES']['SHOP']['VALUE']) {
    $arFilter = array(
        'IBLOCK_ID' => $arResult['PROPERTIES']['SHOP']['LINK_IBLOCK_ID'],
        'ID' => $arResult['PROPERTIES']['SHOP']['VALUE']
    );
    $rsShop = CIBlockElement::GetList(array(), $arFilter)->GetNextElement();
    $arShop = $rsShop->GetFields();
    $arShop['PROPERTIES'] = $rsShop->GetProperties();
    $arResult['SHOP'] = $arShop;
}


if($arResult["PREVIEW_PICTURE"]){
    $img = CFile::ResizeImageGet($arResult["PREVIEW_PICTURE"], array('width'=>2000, 'height'=>2000));
    $arResult["PREVIEW_PICTURE"]["SRC"] = $img["src"];
}else if($arResult["DETAIL_PICTURE"]){
    $img = CFile::ResizeImageGet($arResult["DETAIL_PICTURE"], array('width'=>2000, 'height'=>2000));
    $arResult["PREVIEW_PICTURE"]["SRC"] = $img["src"];
}