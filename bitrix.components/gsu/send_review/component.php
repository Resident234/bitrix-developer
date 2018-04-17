<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
define("CAPTCHA_URL", "https://www.google.com/recaptcha/api/siteverify");
//dump($_REQUEST);
$arResult["FIELDS"] = array(
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

$arResult["CHECK"] = 1;
$arResult["AJAX_ADDRESS"] = $componentPath . "/ajax.php";
$template = "";

if ($_REQUEST["there"]) $arResult["THERE"] = true;
else $arResult["THERE"] = false;

if ($arResult["THERE"]) {

    check($_REQUEST["email"], "EMAIL", $arResult);
    check($_REQUEST["name"], "NAME", $arResult);
    check($_REQUEST["message"], "MESSAGE", $arResult);

    $data = array(
        "secret" => "6Lek-gUTAAAAAOh32QkgWKzkXAzyld4RvRZ9UiGi",
        "response" => str_replace("g-recaptcha-response=", "", $_REQUEST["captcha"])
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, CAPTCHA_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // указываем, что у нас POST запрос
    curl_setopt($ch, CURLOPT_POST, 1);
    // добавляем переменные
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $output = curl_exec($ch);
    $response = json_decode($output, true);
    $arResult["CAPTCHA"]["STATUS"] = $response["success"];
    $arResult["CAPTCHA"]["ERROR"] = "Вы не проверились на робота";

    if (($arResult["CHECK"]) && ($arResult["CAPTCHA"]["STATUS"])) {

        //Добавление в инфоблок
        if (!\Bitrix\Main\Loader::includeModule("iblock")) {
            $this->AbortResultCache();
            ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
            return;
        }


        $el = new CIBlockElement;
        $PROP = array();
        $PROP["EMAIL"] = $_REQUEST["email"];
        $PROP["PRODUCT_ID"] = $arParams["PRODUCT_ID"];

        $arLoadArray = Array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "NAME" => $_REQUEST["name"],
            "DETAIL_TEXT" => $_REQUEST["message"],
            "DATE_ACTIVE_FROM" => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time()),
            "ACTIVE" => "N",
            "PROPERTY_VALUES" => $PROP,
        );

        if ($REVIEW_ID = $el->Add($arLoadArray)) {
            $template = "success";

            $rs = CIBlockElement::GetList(
                Array(),
                Array("IBLOCK_ID" => CATALOG_IBLOCK_ID, "ID" => $arParams["PRODUCT_ID"]),
                false,
                Array(),
                array("ID", "NAME")
            );
            $product = $rs->GetNext();
            //Почтовое событие
            if ($USER->IsAuthorized()) {
                $rsUser = CUser::GetList(($by = "id"), ($order = "asc"), array("ID" => $USER->GetID()));
                $usr = $rsUser->GetNext();

                $arEventFields = array(
                    "NAME" => $_REQUEST["name"],
                    "EMAIL" => $_REQUEST["email"],
                    "PHONE" => $usr["PERSONAL_PHONE"],
                    "USER_ID" => $USER->GetID(),
                    "PRODUCT_ID" => $arParams["PRODUCT_ID"],
                    "PRODUCT_NAME" => $product["NAME"],
                    "ID" => $REVIEW_ID,
                    "PRODUCT_LINK" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".CATALOG_IBLOCK_ID."&type=1c_catalog&ID=".$arParams["PRODUCT_ID"],
                    "REVIEW_LINK" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".REVIEWS_IBLOCK_ID."&type=promodile&ID=".$REVIEW_ID
                );
            } else {
                $arEventFields = array(
                    "NAME" => $_REQUEST["name"],
                    "EMAIL" => $_REQUEST["email"],
                    "PRODUCT_ID" => $arParams["PRODUCT_ID"],
                    "PRODUCT_NAME" => $product["NAME"],
                    "ID" => $REVIEW_ID,
                    "PRODUCT_LINK" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".CATALOG_IBLOCK_ID."&type=1c_catalog&ID=".$arParams["PRODUCT_ID"],
                    "REVIEW_LINK" => "http://".$_SERVER["HTTP_HOST"]."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".REVIEWS_IBLOCK_ID."&type=promodile&ID=".$REVIEW_ID
                );
            }

            CEvent::Send("NEW_REVIEW", PROMOBILE_SITE_ID, $arEventFields);
        } else {
            $template = "form";
        }
    } else $template = "form";
} else  $template = "form";

$this->IncludeComponentTemplate($template);
?>