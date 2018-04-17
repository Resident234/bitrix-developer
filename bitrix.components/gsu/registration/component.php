<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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
			"VALIDATOR" => "/([а-яА-ЯёЁ\s-]){2,}$/u",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнено имя",
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
	);
	$arResult["CHECK"]=1;
	$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";
	$template="";

	if($_REQUEST["there"]) $arResult["THERE"]=true;
	else $arResult["THERE"]=false;

	if($arResult["THERE"]) {

		check($_REQUEST["name"], "NAME", $arResult);
		check($_REQUEST["surname"], "SURNAME", $arResult);
		check($_REQUEST["email"], "EMAIL", $arResult);
		check($_REQUEST["phone"],  "PHONE", $arResult);

		if($arResult["CHECK"]) {

			$conformCode = randString(10);
			$password = randString(10);

			//Добавление пользователя
			$user = new CUser;
			$arFields = Array(
				"NAME"              => $_REQUEST["name"],
				"LAST_NAME"         => $_REQUEST["surname"],
				"EMAIL"             => $_REQUEST["email"],
				"LOGIN"             => $_REQUEST["email"],
				"PERSONAL_PHONE"	=> $_REQUEST["phone"],
				"LID" 				=> "s1",
				"UF_CONFIRM_CODE"  => $conformCode,
				"PASSWORD"          => $password,
				"CONFIRM_PASSWORD"  => $password,
				"UF_DISCOUNT" => 0
			);


			//Проверка, хочет ли пользователь быть оптовиком
			if($_REQUEST["is_opt"]) {
				$_SESSION["OPT_BUYER"] = "Y";
			} else {
				unset($_SESSION["OPT_BUYER"]);
			}

			$ID = $user->Add($arFields);
			if (intval($ID) > 0) {

				//Почтовое событие
				$arEventFields = array(
					"USER_NAME" => $_REQUEST["name"],
					"USER_SURNAME" => $_REQUEST["surname"],
					"USER_LOGIN" => $_REQUEST["email"],
					"USER_EMAIL" => $_REQUEST["email"],
					"CONFIRM_CODE" => $conformCode,
					"USER_ID" => $ID,
					"CREATE_PASSWORD_ADDRESS" => CREATE_PASSWORD_ADDRESS
				);

				CEvent::Send("CONFIRM_REGISTRATION", PROMOBILE_SITE_ID, $arEventFields);
				$template="success";
			}
			else{
				$arResult["FIELDS"]["EMAIL"]["INCORRECT"] = true;
				$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"] = $user->LAST_ERROR;
				$template="form";
			}
		}
		else $template="form";
	}
	else  $template="form";

	$this->IncludeComponentTemplate($template);
?>