<?

	$rsUser = CUser::GetByID($USER->GetID());
	$arResult["USER"] = $rsUser->Fetch();
	$arResult["KEYS"] = array("INDEX","REGION","CITY","STREET","HOME","APARTMENT");
	$arResult["KEYS"]["INPUT"] = array("INDEX","REGION","CITY","STREET","HOME","APARTMENT");

	//Создаю поля для каждого адреса
	$arResult["FIELDS"]=array(
		"INDEX" => array(
			"NAME" => "Индекс",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width",
			"REQUIRED" => false,
			"VALIDATOR" => "/([0-9]){6}$/",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен индекс",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["INDEX"]
		),
		"REGION" => array(
			"NAME" => "Республика, область, край",
			"CSS_CLASS" => "b-form__row b-form__row_b-width",
			"TYPE_FIELD" => "INPUT",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели регион",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["REGION"]
		),
		"CITY" => array(
			"NAME" => "Город",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width no_right",
			"TYPE_FIELD" => "INPUT",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели город",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["CITY"]
		),
		"STREET" => array(
			"NAME" => "Улица, переулок",
			"CSS_CLASS" => "b-form__row b-form__row_xm-width",
			"TYPE_FIELD" => "INPUT",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели улицу",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["STREET"]
		),
		"HOME" => array(
			"NAME" => "Дом, стр., корп.",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width",
			"TYPE_FIELD" => "INPUT",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели дом",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["HOME"]
		),
		"APARTMENT" => array(
			"NAME" => "Квартира",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width",
			"TYPE_FIELD" => "INPUT",
			"REQUIRED" => false,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "",
			"CLASS_ERROR" => "",
			"VALUE" => $arParams["APARTMENT"]
		),


	);

	$arResult["CHECK"]=1;
	$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";
	$arResult["AJAX_DELETE"]=$componentPath."/delete_ajax.php";

	$arResult["NAME"] = $arParams["NAME"];

	if($_REQUEST["there"]) $arResult["THERE"]=true;
	else $arResult["THERE"]=false;


	//Получить значения пропусков и лифта
	if (!\Bitrix\Main\Loader::includeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}



	//Если мы получили данные
	if($arResult["THERE"]) {

		//Проверяем поля
		foreach($arResult["KEYS"] as $key) {

			$val = $arParams[$key];
			$arResult["FIELDS"][$key]["VALUE"] = $val;
			check($val, $key , $arResult);
		}

		//Если все верно обновляем
		if ($arResult["CHECK"]) {



			$arResult["NAME"] = $arParams["INDEX"]." ".$arParams["REGION"]." кр.обл. , г. ".$arParams["CITY"].", ул. ".$arParams["STREET"].", д. ".$arParams["HOME"].", кв./оф. ".$arParams["APARTMENT"];

			$el = new CIBlockElement;

			$PROP = array(
				"INDEX" => $arParams["INDEX"],
				"REGION" => $arParams["REGION"],
				"CITY" => $arParams["CITY"],
				"STREET" => $arParams["STREET"],
				"APARTMENT" => $arParams["APARTMENT"],
				"HOME" => $arParams["HOME"],
				"USER" => $USER->GetID(),
			);

			$arParamArray = Array(
				"PROPERTY_VALUES" => $PROP,
				"NAME" => $arResult["NAME"],
			);

			$res = $el->Update($arParams["ID"], $arParamArray);
			if($res){
				$arResult["SUCCESS"] = 1;
			}
			else{
				$arResult["SUCCESS"] = 0;
				echo $el->LAST_ERROR;
			}

		}
		else $arResult["SUCCESS"] = 0;

	}
	else  $arResult["SUCCESS"] = 0;
 
	$this->IncludeComponentTemplate();
?>