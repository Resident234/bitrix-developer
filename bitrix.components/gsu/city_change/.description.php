<?
    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

    $arComponentDescription = array(
        "NAME" => GetMessage("CITY_CHANGE_NAME"),
        "DESCRIPTION" => GetMessage("CITY_CHANGE_DESCRIPTION"),
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
