<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arResult["FIELDS"] = array(
    "NAME" => array(
        "REQUIRED" => true,
        "VALIDATOR" => "/([а-яА-ЯёЁ\s-]){2,}$/u",
        "INCORRECT" => false,
        "ERROR_MESSAGE" => "Некорректно заполнено имя",
        "CLASS_ERROR" => "",
    ),
    "PHONE" => array(
        "REQUIRED" => true,
        "VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
        "INCORRECT" => false,
        "ERROR_MESSAGE" => "Некорректно заполнен телефон",
        "CLASS_ERROR" => "",
    ),
);
$arResult["CHECK"] = 1;
$arResult["AJAX_ADDRESS"] = $componentPath."/ajax.php";
$template = "";

if ($_REQUEST["there"]) {
    $arResult["THERE"] = true;
} else {
    $arResult["THERE"] = false;
}

if (intval($arParams['MANAGER_FOR_ANSWER']) <= 0 && $USER->IsAuthorized()) {
    $by = "id";
    $order = "asc";
    $dbUser = CUser::GetList($by, $order, array("ID" => $USER->GetID()), array("SELECT" => array("UF_MANAGER_ID"),'FIELDS'=>['ID','PERSONAL_PHONE']));
    if ($dbUser->SelectedRowsCount()) {
        if ($arUser = $dbUser->Fetch()) {
            if (intval($arUser['UF_MANAGER_ID'])) {
                $arParams['MANAGER_FOR_ANSWER'] = $arUser['UF_MANAGER_ID'];
            }
            $arParams['USER_PHONE'] = $arUser['PERSONAL_PHONE'];
            $arParams['USER_NAME'] = $USER->GetFormattedName();
        }
    }
}

if ($arResult["THERE"]) {

    check($_REQUEST["name"], "NAME", $arResult);
    check($_REQUEST["phone"], "PHONE", $arResult);

    if ($arResult["CHECK"]) {

        //Добавление в инфоблок
        if (!\Bitrix\Main\Loader::includeModule("iblock")) {
            $this->AbortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }

        $template = "form";

        $el = new CIBlockElement;
        $PROP = array(
            "CASTOMER_PHONE" => $_REQUEST["phone"],
            "MANAGER_FOR_ANSWER" => intval($arParams["MANAGER_FOR_ANSWER"]),
        );
        $arLoadArray = Array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "NAME" => $_REQUEST["name"],
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $PROP,
        );

        if ($REQUEST_ID = $el->Add($arLoadArray)) {

            if ($arParams["MANAGER_FOR_ANSWER"]) {
                $rsManager = CUser::GetByID($arParams["MANAGER_FOR_ANSWER"]);
                $arManager = $rsManager->Fetch();
                $mail_to = $arManager["EMAIL"];
            } else {
                $rsSites = CSite::GetByID(PROMOBILE_SITE_ID);
                $arSite = $rsSites->Fetch();
                $mail_to = $arSite["EMAIL"];
            }
            //Почтовое событие
            $arEventFields = array(
                "USER_NAME" => $_REQUEST["name"],
                "USER_PHONE" => $_REQUEST["phone"],
                "MANAGER_EMAIL" => $mail_to,
            );
            if ($_REQUEST['email']) {
                $arEventFields['USER_EMAIL'] = $_REQUEST['email'];
            }
            if ($_REQUEST['userId']) {
                $arEventFields['USER_ID'] = $_REQUEST['userId'];
            }
            CEvent::Send("REQUEST_CALL", PROMOBILE_SITE_ID, $arEventFields);

            $template = "success";
        } else {
            $template = "form";
        }
    } else {
        $template = "form";
    }
} else {
    $template = "form";
}
$this->IncludeComponentTemplate($template);
?>