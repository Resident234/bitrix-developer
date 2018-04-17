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

			//Получаю менеджера
			$rsUserManage = CUser::GetByID($arResult["USER"]["UF_MANAGER_ID"]);
			$arResult["MANAGER"] = $rsUserManage->Fetch();

			//ищу совпадения массива, групп, которым принадлежит пользователь, и оптовых покупатлей

			foreach ($optUserGroup as $group) {

				switch($group) {

					case OPT10_GROUP_ID: $arResult["USER"]["PRICE_NAME"] = "Опт10";break;
					case OPT50_GROUP_ID: $arResult["USER"]["PRICE_NAME"] = "Опт50";break;
					case OPT150_GROUP_ID: $arResult["USER"]["PRICE_NAME"] = "Опт150";break;
					case OPT300_GROUP_ID: $arResult["USER"]["PRICE_NAME"] = "Опт300";break;
					case SPEC_GROUP_ID: $arResult["USER"]["PRICE_NAME"] = "Спец";break;
				}
			}

		}
	}
	$this->IncludeComponentTemplate($template);
?>