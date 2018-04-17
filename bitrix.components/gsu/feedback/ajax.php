<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
define("STOP_STATISTICS", true);
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

define("NO_AGENT_CHECK", true);
define("NO_AGENT_STATISTIC", true);
define("PUBLIC_AJAX_MODE", true);

$name = $_REQUEST["name"];
$phone = $_REQUEST["phone"];
$email = $_REQUEST["email"];

dump($email);

if($name&&$phone&&$email){

    $arEventFields = array(
        "FIO" => $name,
        "MAIL" => $email
    );
    CEvent::Send("NEW_APPLY_USER", "s3", $arEventFields);
    unset($arEventFields);

    $arEventFields = array(
        "FIO" => $name,
        "MAIL" => $email,
        "PHONE" => $phone,
        "TIME" => ConvertTimeStamp(time(), "FULL", "ru"),
        "CODE" => ""
    );
    CEvent::Send("NEW_APPLY_ORDER", "s3", $arEventFields);
}