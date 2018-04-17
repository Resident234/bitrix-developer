<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!\Bitrix\Main\Loader::includeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CP_BCSL_IBLOCK_ID"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
		),
		"PRODUCT_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PRODUCT_ID"),
			"TYPE" => "STRING",
		),
		"PRODUCT_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PRODUCT_NAME"),
			"TYPE" => "STRING",
		),
		"USER_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("USER_NAME"),
			"TYPE" => "STRING",
		),
		"USER_EMAIL" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("USER_EMAIL"),
			"TYPE" => "STRING",
		),
	),
);
?>