<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	if (!CModule::IncludeModule("sale")) return;

    $arResult["ITEMS"] = array();

    $db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "!CITY_NAME"=>false), false, array("nTopCount"=>27), array());
    while ($vars = $db_vars->Fetch()) {
        $ar = array();
        $ar["ID"] = $vars["CITY_ID"];
        $ar["NAME"] = $vars["CITY_NAME"];
        $arResult["ITEMS"][] = $ar;
    }

	$this->IncludeComponentTemplate();
?>