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
		"USER_NAME" =>array(
			"PARENT" => "BASE",
			"NAME" => "Имя пользователя",
			"TYPE" => "STRING",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => "",
			"REFRESH" => "Y",
		),
		"USER_PHONE" =>array(
			"PARENT" => "BASE",
			"NAME" => "Телефон пользователя",
			"TYPE" => "STRING",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => "",
			"REFRESH" => "Y",
		),
		"MANAGER_FOR_ANSWER" => array(
			"PARENT" => "BASE",
			"NAME" => "Менеджер для ответа",
			"TYPE" => "STRING",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => "",
			"REFRESH" => "Y",
		),
	),
);
?>