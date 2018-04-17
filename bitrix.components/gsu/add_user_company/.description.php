<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    $arComponentDescription = array(
        "NAME" => GetMessage("NAME"),
        "DESCRIPTION" => GetMessage("DESCRIPTION"),
        "ICON" => "/images/sections_top_count.gif",
        "CACHE_PATH" => "Y",
        "SORT" => 20,
        "PATH" => array(
            "ID" => "content",
            "CHILD" => array(
                "ID" => "catalog_ext",
                "NAME" => GetMessage("SECTION_NAME"),
                "SORT" => 30,
                "CHILD" => array(
                    "ID" => "catalog_cmpx",
                ),
            ),
        ),
    );
?>
