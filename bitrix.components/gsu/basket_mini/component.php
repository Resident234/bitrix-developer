<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

	if (!CModule::IncludeModule("sale")) return;

	$dbBasketItems = CSaleBasket::GetList(
		array(),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"ORDER_ID" => "NULL",
			"LID" => SITE_ID,
		),
		false,
		false,
		array("ID","QUANTITY","PRICE")
	);

	$arResult["GENERAL_COST"]=0;
	$arResult["COUNT"]=0;

	while ($arItems = $dbBasketItems->Fetch()) {

		$arResult["COUNT"] += $arItems["QUANTITY"];
		$arResult["GENERAL_COST"] += $arItems["QUANTITY"]*$arItems["PRICE"];
	}

	if($arParams["SHOW_COST"]=="Y") $template="with_cost";
	else $template="without_cost";

	$this->IncludeComponentTemplate($template);
?>