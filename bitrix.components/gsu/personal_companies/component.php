<?

	$rsUser = CUser::GetByID($USER->GetID());
	$arResult["USER"] = $rsUser->Fetch();

	$arGroupUser = CUser::GetUserGroup($USER->GetID());
	$arOptGroup = getOptGroup();
	$optUserGroup = array_intersect($arOptGroup, $arGroupUser);

	if(count($optUserGroup)>0) $arResult["IS_OPT_USER"] = true;
	else $arResult["IS_OPT_USER"] = false;



if($USER->IsAuthorized()&&$arResult["IS_OPT_USER"]) {
	//Получаем адреса пользователя
	if (!\Bitrix\Main\Loader::includeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}

	$arSelect = Array("ID", "NAME",);
	$arFilter = Array(
		"IBLOCK_ID" => USERS_COMPANY_IBLOCK_ID,
		"PROPERTY_USER" => $arResult["USER"]["ID"]
	);

	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while ($ob = $res->GetNextElement()) {
		$arResult["COMPANIES"][] = $ob->GetFields();
	}

}



$this->IncludeComponentTemplate();

?>