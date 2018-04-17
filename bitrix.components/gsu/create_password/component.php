<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	$arResult["STATUS"] = array(
		"ERROR" => false,
		"MESSAGE" => ""
	);

	$USER_ID = $arParams["USER_ID"];
	$CONFIRM_CODE = $arParams["CONFIRM_CODE"];

	$rsUser = CUser::GetByID($USER_ID);
	$arUser = $rsUser->Fetch();

	if($arUser["UF_CONFIRM_CODE"]==$CONFIRM_CODE) {

		$arResult["AJAX_ADDRESS"] = $componentPath . "/ajax.php";

		if ($_REQUEST["there"]) $arResult["THERE"] = true;
		else $arResult["THERE"] = false;

		if ($arResult["THERE"]) {


			$user = new CUser;
			$fields = Array(

				"PASSWORD"          => $_REQUEST["password"],
				"CONFIRM_PASSWORD"  => $_REQUEST["repeat_password"],
			);
			$successUpdate = $user->Update($USER_ID, $fields);

			if($successUpdate) {
				$arResult["STATUS"]["ERROR"] = false;

				global $USER;
				$successAuthorize = $USER->Authorize($USER_ID);

				if($successAuthorize){

					//Почтовое событие
					$arEventFields = array(
						"USER_NAME" => $arUser["NAME"],
						"USER_SURNAME" => $arUser["LAST_NAME"],
						"USER_EMAIL" => $arUser["EMAIL"],
						"USER_LOGIN" => $arUser["LOGIN"],
						"USER_PASSWORD" => $_REQUEST["password"],
						"USER_ID" => $USER_ID,
						"USER_PHONE" => $arUser['PERSONAL_PHONE']
					);

					if(in_array(WANT_BE_OPT_GROUP_ID,CUser::GetUserGroup($USER_ID))){
						$arEventFields["IS_OPT"] = "
							<tr>
								<td>Желает быть оптовым покупателем.</td>
							 </tr>";
						$arEventFields['USER_IS_OPT']="Да";
					}
					else{
						$arEventFields["IS_OPT"] = "";
						$arEventFields['USER_IS_OPT']="Нет";
					}
					CEvent::Send("NEW_USER_REG", PROMOBILE_SITE_ID, $arEventFields);

					$template = "success";
				}
				else {
					$arResult["STATUS"]["ERROR"] = true;
					$arResult["STATUS"]["MESSAGE"] = $user->LAST_ERROR;
					$template = "form";
				}

			}
			else{
				$arResult["STATUS"]["ERROR"] = true;
				$arResult["STATUS"]["MESSAGE"] = $user->LAST_ERROR;
				$template = "form";
			}

		}
		else  $template = "form";
	}
	else $template = "error_link";
	$this->IncludeComponentTemplate($template);

?>