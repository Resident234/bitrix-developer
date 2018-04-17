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
			"VALUE" => ""
		),
		"REGION" => array(
			"NAME" => "Республика, область, край",
			"CSS_CLASS" => "b-form__row b-form__row_b-width",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели регион",
			"CLASS_ERROR" => "",
			"VALUE" => $_REQUEST["REGION"]
		),
		"CITY" => array(
			"NAME" => "Город",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width no_right",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели город",
			"CLASS_ERROR" => "",
			"VALUE" => $_REQUEST["CITY"]
		),
		"STREET" => array(
			"NAME" => "Улица, переулок",
			"CSS_CLASS" => "b-form__row b-form__row_xm-width",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели улицу",
			"CLASS_ERROR" => "",
			"VALUE" => $_REQUEST["STREET"]
		),
		"HOME" => array(
			"NAME" => "Дом",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width",
			"REQUIRED" => true,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Не ввели дом",
			"CLASS_ERROR" => "",
			"VALUE" => $_REQUEST["HOME"]
		),
		"APARTMENT" => array(
			"NAME" => "Квартира",
			"CSS_CLASS" => "b-form__row b-form__row_sm-width",
			"REQUIRED" => false,
			"VALIDATOR" => false,
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "",
			"CLASS_ERROR" => "",
			"VALUE" => $_REQUEST["APARTMENT"]
		),

	);

	if (!\Bitrix\Main\Loader::includeModule("iblock")) {
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}




	$arResult["CHECK"]=1;
	$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";

	if($_REQUEST["there"]) $arResult["THERE"]=true;
	else $arResult["THERE"]=false;

	//Если мы получили данные
	if($arResult["THERE"]) {

		//Проверяем поля
		foreach($arResult["KEYS"]["INPUT"] as $key) {

			$val = $_REQUEST[$key];
			$arResult["FIELDS"][$key]["VALUE"] = $val;
			check($val, $key , $arResult);
		}

		//Если все верно обновляем
		if ($arResult["CHECK"]) {


			$el = new CIBlockElement;

			$PROP = array(
				"INDEX" => $_REQUEST["INDEX"],
				"REGION" => $_REQUEST["REGION"],
				"CITY" => $_REQUEST["CITY"],
				"STREET" => $_REQUEST["STREET"],
				"APARTMENT" => $_REQUEST["APARTMENT"],
				"HOME" => $_REQUEST["HOME"],
				"USER" => $USER->GetID(),
			);

			$arParamArray = Array(
				"PROPERTY_VALUES" => $PROP,
				"NAME" => $_REQUEST["INDEX"]." ".$_REQUEST["REGION"]." кр.обл. , г. ".$_REQUEST["CITY"].", ул. ".$_REQUEST["STREET"].", д. ".$_REQUEST["HOME"].", кв./оф. ".$_REQUEST["APARTMENT"],
				"IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
			);

			$res = $el->Add($arParamArray);
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