<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

if (!\Bitrix\Main\Loader::includeModule("iblock")) {
    $this->AbortResultCache();
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

$arSelect = Array(
    "ID",
    "NAME",
    "PROPERTY_INDEX",
    "PROPERTY_REGION",
    "PROPERTY_CITY",
    "PROPERTY_STREET",
    "PROPERTY_HOME",
    "PROPERTY_APARTMENT",
    "PROPERTY_FLOOR",
    "PROPERTY_ELEVATOR",
    "PROPERTY_PASS",
    "PROPERTY_USER"
);
$arFilter = Array(
    "IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
    "ID" => $_REQUEST["ID"],
);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

while ($ob = $res->GetNextElement()) {
    $result = $ob->GetFields();
    $arResult["ADDRESS_DELIVERY"] = array(
        "ID" => $result["ID"],
        "DELIVERY_INDEX" => $result["PROPERTY_INDEX_VALUE"],
        "DELIVERY_REGION" => $result["PROPERTY_REGION_VALUE"],
        "DELIVERY_CITY" => $result["PROPERTY_CITY_VALUE"],
        "DELIVERY_STREET" => $result["PROPERTY_STREET_VALUE"],
        "DELIVERY_HOME" => $result["PROPERTY_HOME_VALUE"],
        "DELIVERY_APARTMENT" => $result["PROPERTY_APARTMENT_VALUE"],
        "DELIVERY_FLOOR" => $result["PROPERTY_FLOOR_VALUE"],
        "DELIVERY_ELEVATOR" => $result["PROPERTY_ELEVATOR_VALUE"],
        "DELIVERY_PASS" => $result["PROPERTY_PASS_VALUE"],
        "DELIVERY_USER" => $result["PROPERTY_USER_VALUE"],
    );
}

echo json_encode($arResult["ADDRESS_DELIVERY"]);