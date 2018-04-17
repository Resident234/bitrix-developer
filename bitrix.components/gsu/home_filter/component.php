<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


	//Добавление в инфоблок
	if (!\Bitrix\Main\Loader::includeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	$arOrder = array("NAME" => "ASC");
	$arFilter = Array(
		'IBLOCK_ID' => DEVICES_IBLOCK_ID,
		'SECTION_ID ' => DEVICES_SECTION_ID,
		"DEPTH_LEVEL" => 2,
		'GLOBAL_ACTIVE'=>'Y',
	);
	$arSelect = array("ID","NAME");
	$res = CIBlockSection::GetList($arOrder, $arFilter,false,$arSelect);
	while($section = $res->GetNext()) {
		$arResult["PRODUCERS"][] = $section;
	}


$this->IncludeComponentTemplate();
?>