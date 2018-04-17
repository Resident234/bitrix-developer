<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();


	$arResult["FIELDS"]=array(
		"EMAIL" => array(
			"REQUIRED" => true,
			"VALIDATOR" => "/([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i",
			"INCORRECT" => false,
			"ERROR_MESSAGE" => "Некорректно заполнен email",
			"CLASS_ERROR" => ""
		),
	);
	$arResult["CHECK"]=1;

	$USER_ID = $arParams["USER_ID"];
	$CONFIRM_CODE = $arParams["CONFIRM_CODE"];

	$rsUser = CUser::GetByID($USER_ID);
	$arUser = $rsUser->Fetch();


	if($arUser["UF_CONFIRM_CODE"]==$CONFIRM_CODE) {

		$arResult["AJAX_ADDRESS"] = $componentPath . "/ajax.php";

		if ($_REQUEST["there"]) $arResult["THERE"] = true;
		else $arResult["THERE"] = false;

		if ($arResult["THERE"]) {

			check($_REQUEST["email"], "EMAIL", $arResult);

			if($arResult["CHECK"]) {

				$user = new CUser;
				$fields = Array(
					"EMAIL" => $_REQUEST["email"],
				);
				$successUpdate = $user->Update($USER_ID, $fields);

				if ($successUpdate) {


					$arEventFields = array(
						"USER_NAME" => $arUser["NAME"],
						"USER_SURNAME" => $arUser["LAST_NAME"],
						"USER_EMAIL" => $_REQUEST["email"],
						"USER_LOGIN" =>"В случае регистрации через социальные сервисы логин не нужен",
						"USER_PASSWORD" => "В случае регистрации через социальные сервисы пароль не нужен",
						"USER_ID" => $USER_ID,
					);

					if(in_array(WANT_BE_OPT_GROUP_ID,CUser::GetUserGroup($arFields["ID"]))){
						$arEventFields["IS_OPT"] = "
							<tr>
								<td>Желает быть оптовым покупателем.</td>
							 </tr>";
					}
					else{
						$arEventFields["IS_OPT"] = "";
					}
					CEvent::Send("NEW_USER_REG", PROMOBILE_SITE_ID, $arEventFields);

					global $USER;
					$USER->Authorize($USER_ID);
					$template = "success";

				} else {
					$arResult["FIELDS"]["EMAIL"]["INCORRECT"] = true;
					$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"] = $user->LAST_ERROR;
					$template = "form";
				}
			}
			else $template = "form";
		}
		else  $template = "form";
	}
	else $template = "error_link";
	$this->IncludeComponentTemplate($template);

?>