<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arResult = array();
if ((!empty($_GET['utm_source'])
        || !empty($_GET['utm_medium'])
        || !empty($_GET['utm_campaign'])
        || !empty($_GET['utm_content'])
        || !empty($_GET['utm_term'])
    ) && !$_SESSION['utm']) {

    $_SESSION['utm'] = true;
    $first = true;
    $_SESSION['utm_source'] = $_GET['utm_source'];
    $_SESSION['utm_medium'] = $_GET['utm_medium'];
    $_SESSION['utm_campaign'] = $_GET['utm_campaign'];
    $_SESSION['utm_content'] = $_GET['utm_content'];
    $_SESSION['utm_term'] = $_GET['utm_term'];
}
if (!empty($first)) {
    $arResult["UTM_SOURCE"] = $_GET['utm_source'];
    $arResult["UTM_MEDIUM"] = $_GET['utm_medium'];
    $arResult["UTM_CAMPAIGN"] = $_GET['utm_campaign'];
    $arResult["UTM_CONTENT"] = $_GET['utm_content'];
    $arResult["UTM_TERM"] = $_GET['utm_term'];
} else {
    $arResult["UTM_SOURCE"] = $_SESSION['utm_source'];
    $arResult["UTM_MEDIUM"] = $_SESSION['utm_medium'];
    $arResult["UTM_CAMPAIGN"] = $_SESSION['utm_campaign'];
    $arResult["UTM_CONTENT"] = $_SESSION['utm_content'];
    $arResult["UTM_TERM"] = $_SESSION['utm_term'];
}

$this->IncludeComponentTemplate();