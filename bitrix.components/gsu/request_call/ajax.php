<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$APPLICATION->IncludeComponent(
    "peppers:request_call",
    "",
    Array(
        "IBLOCK_TYPE" => "feedback",
        "IBLOCK_ID" => REQUEST_CALL_IBLOCK_ID,
        "MANAGER_FOR_ANSWER" => $_REQUEST["manager_id"],
    )
);?>