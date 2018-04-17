<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$APPLICATION->IncludeComponent(
    "peppers:edit_user_addresses",
    "",
    Array(
        "NAME" => $_REQUEST["NAME"],
        "ID" => $_REQUEST["ID"],
        "INDEX" => $_REQUEST["INDEX"],
        "REGION" => $_REQUEST["REGION"],
        "CITY" => $_REQUEST["CITY"],
        "STREET" => $_REQUEST["STREET"],
        "HOME" => $_REQUEST["HOME"],
        "APARTMENT" => $_REQUEST["APARTMENT"],
        "FLOOR" => $_REQUEST["FLOOR"],
        "ELEVATOR" => $_REQUEST["ELEVATOR"],
        "PASS" => $_REQUEST["PASS"],
    )
);?>