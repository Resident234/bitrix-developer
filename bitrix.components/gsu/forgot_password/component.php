<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult["FIELDS"] = array(
    "EMAIL" => array(
        "REQUIRED" => true,
        "VALIDATOR" => "/([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i",
        "INCORRECT" => false,
        "ERROR_MESSAGE" => "Некорректно заполнен email",
        "CLASS_ERROR" => ""
    ),
);
$arResult["CHECK"] = 1;
$arResult["AJAX_ADDRESS"] = $componentPath . "/ajax.php";
$template = "";

if ($_REQUEST["there"]) $arResult["THERE"] = true;
else $arResult["THERE"] = false;

if ($arResult["THERE"]) {

    check($_REQUEST["email"], "EMAIL", $arResult);


    if ($arResult["CHECK"]) {

        $conformCode = randString(10);

        $arBy = "email";
        $order = "desc";
        $arFilter = array(
            "ACTIVE" => "Y",
            "EMAIL" => $_REQUEST["email"]
        );
        $rsUsers = CUser::GetList($arBy, $order, $arFilter);
        while ($arFields = $rsUsers->GetNext()) {
            $idUser = $arFields["ID"];
            $arUser = $arFields;
        }

        if ($idUser) {


            $user = new CUser;
            $arFields = Array("UF_CONFIRM_CODE" => $conformCode,);
            $res = $user->Update($idUser, $arFields);

            if ($res) {

                $restoreLink = "http://" . $_SERVER["HTTP_HOST"] . RECOVERY_PASSWORD_ADDRESS ."?ID=".$idUser."&CONFIRM_CODE=".$conformCode;

                //Почтовое событие
                $arEventFields = array(
                    "USER_ID" => $arUser["ID"],
                    "NAME" => $arUser["NAME"],
                    "LAST_NAME" => $arUser["LAST_NAME"],
                    "LOGIN" => $arUser["LOGIN"],
                    "EMAIL" => $_REQUEST["email"],
                    "LINK" => $restoreLink
                );
                CEvent::Send("USER_PASS_REQUEST", PROMOBILE_SITE_ID, $arEventFields);
                $template = "success";
            } else {
                $arResult["FIELDS"]["EMAIL"]["INCORRECT"] = true;
                $arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"] = $user->LAST_ERROR;
                $template = "form";
            }

        } else {
            $arResult["FIELDS"]["EMAIL"]["INCORRECT"] = true;
            $arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"] = "Пользователя с таким email нет";
            $template = "form";
        }

    } else $template = "form";
} else  $template = "form";

$this->IncludeComponentTemplate($template);
?>