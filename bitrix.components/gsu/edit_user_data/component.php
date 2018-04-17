<?

	$rsUser = CUser::GetByID($USER->GetID());
	$arResult["USER"] = $rsUser->Fetch();
	//dump($arResult["USER"]);
	//Обработка формы
	$arResult["FIELDS"]=array(
		"NAME" => array(
			"REQUIRED" => true,
			"VALIDATOR" => "/([а-яА-ЯёЁ\s-]){2,}$/u",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнено имя",
			"CLASS_ERROR" => ""
		),
		"SURNAME" => array(
			"REQUIRED" => false,
			"VALIDATOR" => "/([а-яА-Я\s-]){2,}$/u",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполненa фамилия",
			"CLASS_ERROR" => ""
		),
		"SECOND_NAME" => array(
			"REQUIRED" => false,
			"VALIDATOR" => "/([а-яА-Я\s-]){2,}$/u",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнено отчество",
			"CLASS_ERROR" => ""
		),
		"PHONE" => array(
			"REQUIRED" => true,
			"VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен телефон",
			"CLASS_ERROR" => ""
		),
		"EMAIL" => array(
			"REQUIRED" => true,
			"VALIDATOR" => "/([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен email",
			"CLASS_ERROR" => ""
		),
		"PHONE1" => array(
			"REQUIRED" => false,
			"VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен телефон",
			"CLASS_ERROR" => ""
		),
		"PHONE2" => array(
			"REQUIRED" => false,
			"VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен телефон",
			"CLASS_ERROR" => ""
		),
		"PHONE3" => array(
			"REQUIRED" => false,
			"VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен телефон",
			"CLASS_ERROR" => ""
		),
	);
	$arResult["CHECK"]=1;
	$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";
	$template="";

	if($_REQUEST["there"]) $arResult["THERE"]=true;
	else $arResult["THERE"]=false;

	if($arResult["THERE"]) {

		check($_REQUEST["name"], "NAME", $arResult);
		check($_REQUEST["surname"], "SURNAME", $arResult);
		check($_REQUEST["secondname"], "SECOND_NAME", $arResult);
		check($_REQUEST["email"], "EMAIL", $arResult);
		check($_REQUEST["phone"],  "PHONE", $arResult);

		$counter=1;
		foreach($_REQUEST["other_phones"] as $otherPhone){
			check($_REQUEST["phone".$counter],  "PHONE".$counter, $arResult);
			$counter++;
		}
		if($arResult["CHECK"]) {

			$user = new CUser;
			$fields = Array(
				"NAME" => $_REQUEST["name"],
				"LAST_NAME" => $_REQUEST["surname"],
				"SECOND_NAME" => $_REQUEST["secondname"],
				"PERSONAL_PHONE" => $_REQUEST["phone"],
				"EMAIL" => $_REQUEST["email"],
			);

			$arOtherPhones=array();

			foreach($_REQUEST["other_phones"] as $otherPhone){
				if($otherPhone) $arOtherPhones[]=$otherPhone;
			}
			$fields["UF_PHONE_NUMBERS"] = $arOtherPhones;
			$user->Update($arResult["USER"]["ID"], $fields);

			$arResult["SUCCESS"] = 1;
		}
		else $arResult["SUCCESS"] = 0;
	}
	else  $arResult["SUCCESS"] = 0;

	$this->IncludeComponentTemplate($template);
?>