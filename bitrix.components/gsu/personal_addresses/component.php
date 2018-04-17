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
	);
	$arFilter = Array(
		"IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
		"PROPERTY_USER" => $arResult["USER"]["ID"]
	);

	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while ($ob = $res->GetNextElement()) {
		$result = $ob->GetFields();
		//dump($result);
		$arResult["ADDRESSES"][$result["ID"]] = array(
			"NAME" => $result["NAME"],
			"ID" => $result["ID"],
			"INDEX" => $result["PROPERTY_INDEX_VALUE"],
			"REGION" => $result["PROPERTY_REGION_VALUE"],
			"CITY" => $result["PROPERTY_CITY_VALUE"],
			"STREET" => $result["PROPERTY_STREET_VALUE"],
			"HOME" => $result["PROPERTY_HOME_VALUE"],
			"APARTMENT" => $result["PROPERTY_APARTMENT_VALUE"],
			"FLOOR" => $result["PROPERTY_FLOOR_VALUE"],
			"ELEVATOR" => $result ["PROPERTY_ELEVATOR_ENUM_ID"],
			"PASS" => $result ["PROPERTY_PASS_ENUM_ID"],
		);
	}
}

	$this->IncludeComponentTemplate();
?>