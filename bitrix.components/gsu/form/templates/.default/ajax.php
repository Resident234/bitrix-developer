<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$APPLICATION->IncludeComponent(
    "peppers:peppers_form",
    "personal_companies",
    Array(
        "ID" => $_REQUEST["ID"],
        "DESCRIPTION_FIELDS" => get_company_form_description(),
        "WRAP_CLASS" => $_REQUEST["WRAP_CLASS"]
    )
);?>