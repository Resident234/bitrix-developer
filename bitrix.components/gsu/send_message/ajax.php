<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$APPLICATION->IncludeComponent(
    "peppers:send_message",
    "",
    Array(
        "IBLOCK_TYPE" => "feedback",
        "IBLOCK_ID" => MESSAGES_IBLOCK_ID,
        "MANAGER_FOR_ANSWER" => $_REQUEST["manager_id"]
    )
);?>