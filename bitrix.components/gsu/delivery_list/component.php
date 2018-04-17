<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

if (!CModule::IncludeModule("sale")) die;

$ID = 0;
if ($APPLICATION->get_cookie("CITY_ID")) {
    $ID = $APPLICATION->get_cookie("CITY_ID");
} elseif ($_SESSION["CITY_ID"]) {
    $ID = $_SESSION["CITY_ID"];
}

if ($this->StartResultCache(3600000, implode(',', array($ID)))) {

    $arResult['TABS'] = array(
        1 => array(
            'NAME' => 'Розничный покупатель',
            'CODE' => 'RETAIL',
            'DELIVERIES' => DeliveriesList::getRetailDeliveries()
        ),
        2 => array(
            'NAME' => 'Оптовый покупатель',
            'CODE' => 'OPT',
            'DELIVERIES' => DeliveriesList::getOptDeliveries()
        )
    );
    $arResult['TRANSPORT_COMPANIES'] = Helper::getTransportCompanies();
    $arResult['CURRENT_LOCATION'] = Helper::getLocationIdByCityId($ID);
    $arResult['PICKUP_LIST'] = Helper::getPickupListByBitrixLocation($ID);
    $arResult['DENIED_DELIVERIES'] = DeliveriesList::getDeniedDeliveries((bool)count($arResult['PICKUP_LIST']));
    $arResult['AJAX_PATH'] = $APPLICATION->GetCurPage();

    $deliveriesList = new DeliveriesList($arResult['CURRENT_LOCATION']);
    $arResult['DELIVERIES'] = $deliveriesList->deleteDeniedDeliveries($arResult['DENIED_DELIVERIES'])->getAllOrderByCode();

    $this->IncludeComponentTemplate();
}