<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
define("CAPTCHA_URL","https://www.google.com/recaptcha/api/siteverify");

	$arResult["FIELDS"]=array(
	"NAME" => array(
		"REQUIRED" => true,
			"VALIDATOR" => "/([а-яА-ЯёЁ\s-]){2,}$/u",
		"INCORRECT" => false,
		"ERROR_MESSAGE" => "Некорректно заполнено имя",
		"CLASS_ERROR" => ""
	),
	"EMAIL" => array(
		"REQUIRED" => true,
		"VALIDATOR" => "/([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i",
		"INCORRECT" => false,
		"ERROR_MESSAGE" => "Некорректно заполнен email",
		"CLASS_ERROR" => ""
	),
	"MESSAGE" => array(
		"REQUIRED" => true,
		"VALIDATOR" => "/[\w\W]$/u",
		"INCORRECT" => false,
		"ERROR_MESSAGE" => "Поле не заполнено",
		"CLASS_ERROR" => ""
	)
);

	$arResult["CHECK"]=1;
	$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";
	$template="";



	if($_REQUEST["there"]) {


		check($_REQUEST["email"], "EMAIL", $arResult);
		check($_REQUEST["name"], "NAME", $arResult);
		check($_REQUEST["message"],  "MESSAGE", $arResult);

		$data= array(
			"secret" => "6Lek-gUTAAAAAOh32QkgWKzkXAzyld4RvRZ9UiGi",
			"response" => str_replace("g-recaptcha-response=","",$_REQUEST["captcha"])
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, CAPTCHA_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// указываем, что у нас POST запрос
		curl_setopt($ch, CURLOPT_POST, 1);
		// добавляем переменные
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		$output = curl_exec($ch);
		$response = json_decode($output,true);
		$arResult["CAPTCHA"]["STATUS"]=$response["success"];
		$arResult["CAPTCHA"]["ERROR"]="Вы не проверились на робота";



		if(($arResult["CHECK"])&&($arResult["CAPTCHA"]["STATUS"])) {

			//Добавление в инфоблок
			if (!\Bitrix\Main\Loader::includeModule("iblock")) {
				$this->AbortResultCache();
				ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
				return;
			}

			$el = new CIBlockElement;
			$PROP = array();
			$PROP["EMAIL"] = $_REQUEST["email"];
			$PROP["MESSAGE"] = $_REQUEST["message"];
			$arLoadArray = Array(
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"NAME" => $_REQUEST["name"],
				"ACTIVE" => "Y",
				"PROPERTY_VALUES" => $PROP,
			);

			if ($MESSAGE_ID = $el->Add($arLoadArray)) {
				$template="success";

				//Почтовое событие
				$arEventFields = array(
					"NAME" => $_REQUEST["name"],
					"EMAIL" => $_REQUEST["email"],
					"MESSAGE" => $_REQUEST["message"]
				);
				CEvent::Send("NEW_LETTER", PROMOBILE_SITE_ID, $arEventFields);
			}
			else {
				$template="form";
			}
		}
		else $template="form";
	}
	else  $template="form";

	$this->IncludeComponentTemplate($template);
?>