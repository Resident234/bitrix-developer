<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	if (!\Bitrix\Main\Loader::includeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arSelect = Array("ID", "NAME",);

	$arFilter = Array(
		"IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
		"PROPERTY_USER" => $USER->GetID(),
	);
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while ($ob = $res->GetNextElement()) {
		$result = $ob->GetFields();
		$arResult["ADDRESS_DELIVERY"][] = $result;
	}
	
	$this->IncludeComponentTemplate($template);
?>