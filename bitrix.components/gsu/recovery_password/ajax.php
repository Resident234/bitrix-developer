<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$APPLICATION->IncludeComponent(
    "peppers:recovery_password",
    "",
    Array(
        "CONFIRM_CODE" => $_REQUEST["CONFIRM_CODE"],
        "USER_ID" => $_REQUEST["ID"],
    ),
    false
);?>