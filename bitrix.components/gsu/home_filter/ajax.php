<?
header('Content-type: application/json');

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

$arModels = array();

$arOrder = array("NAME" => "DESC");
$arFilter = Array(
    'ACTIVE' => "Y",
    'IBLOCK_ID' => DEVICES_IBLOCK_ID,
    'SECTION_ID' => $_REQUEST["id"]
);
$arSelect = array("ID","NAME","XML_ID","CODE");

$res = CIBlockElement::GetList($arOrder, $arFilter,false,false,$arSelect);
while($model = $res->GetNext()) {
    $arModels[] = array(
        "NAME" => $model["NAME"],
        //"XML_ID" => $model["XML_ID"]
        //"XML_ID" => abs(crc32($model["XML_ID"]))
        "CODE" => $model["CODE"],
    );
}

echo json_encode($arModels);

;?>