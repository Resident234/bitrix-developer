<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => "Заявка на прайс",
    "DESCRIPTION" => "",
    "ICON" => "/images/like_buttons.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 20,
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "content-social",
            "NAME" => "Свои компоненты",
            "SORT" => 300,
        )
    )
);
?>