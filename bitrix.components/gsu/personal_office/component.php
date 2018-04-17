<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($USER->IsAuthorized()) {
	$rsUser = CUser::GetByID($USER->GetID());
	$arResult["USER"] = $rsUser->Fetch();

	$arUserGroup = CUser::GetUserGroup($arResult["USER"]["ID"]);

	//ищу совпадения массива, групп, которым принадлежит пользователь, и оптовых покупатлей
	$arOptGroup = getOptGroup();
	$optUserGroup = array_intersect($arOptGroup, $arUserGroup);

	//Узнаю, пользователь оптовый или нет
	if (in_array(RETAIL_GROUP_ID, $arUserGroup)) $arResult["USER"]["IS_OPT"] = false;
	elseif (in_array(WANT_BE_OPT_GROUP_ID, $arUserGroup)) $arResult["USER"]["IS_OPT"] = false;
	elseif(count($optUserGroup)>0) $arResult["USER"]["IS_OPT"] = true;
	else $arResult["USER"]["IS_OPT"] = false;

	//Если оптовый...
	if ($arResult["USER"]["IS_OPT"]) {

		$rsUserManage = CUser::GetByID($arResult["USER"]["UF_MANAGER_ID"]);
		$arResult["MANAGER"] = $rsUserManage->Fetch();

		if (!\Bitrix\Main\Loader::includeModule("iblock")) {
			$this->AbortResultCache();
			ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
			return;
		}

		//получение моих компаний
		$arSelect = Array("ID", "NAME");
		$arFilter = Array("IBLOCK_ID" => USERS_COMPANY_IBLOCK_ID, "PROPERTY_USER" => $arResult["USER"]["ID"]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arResult["USER"]["COMPANIES"][] = $arFields["NAME"];
		}

		//получение моих адресов
		$arSelect = Array("ID", "NAME");
		$arFilter = Array("IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID, "PROPERTY_USER" => $arResult["USER"]["ID"]);
		$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();
			$arResult["USER"]["ADDRESSES"][] = $arFields["NAME"];
		}
	}

}
	$this->IncludeComponentTemplate();
?>