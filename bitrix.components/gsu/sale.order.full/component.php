<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SALE_MODULE_NOT_INSTALL"));
	return;
}

if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_delivery/delivery_shoplogistics.php"))
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/php_interface/include/sale_delivery/delivery_shoplogistics.php");

/*----------------------Установка параметров-----------------------*/
$arParams["PATH_TO_BASKET"] = Trim($arParams["PATH_TO_BASKET"]);
if (strlen($arParams["PATH_TO_BASKET"]) <= 0)
	$arParams["PATH_TO_BASKET"] = "basket.php";

$arParams["PATH_TO_PERSONAL"] = Trim($arParams["PATH_TO_PERSONAL"]);
if (strlen($arParams["PATH_TO_PERSONAL"]) <= 0)
	$arParams["PATH_TO_PERSONAL"] = "index.php";

$arParams["PATH_TO_PAYMENT"] = Trim($arParams["PATH_TO_PAYMENT"]);
if (strlen($arParams["PATH_TO_PAYMENT"]) <= 0)
	$arParams["PATH_TO_PAYMENT"] = "payment.php";

$arParams["PATH_TO_AUTH"] = Trim($arParams["PATH_TO_AUTH"]);
if (strlen($arParams["PATH_TO_AUTH"]) <= 0)
	$arParams["PATH_TO_AUTH"] = "/auth.php";

$arParams["ALLOW_PAY_FROM_ACCOUNT"] = (($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "N") ? "N" : "Y");
$arParams["COUNT_DELIVERY_TAX"] = (($arParams["COUNT_DELIVERY_TAX"] == "Y") ? "Y" : "N");
$arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] = (($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N");
if(!isset($arParams["PATH_TO_ORDER"]))
{
	$arParams["PATH_TO_ORDER"] = $APPLICATION->GetCurPage();
}
$arParams["SHOW_MENU"] = ($arParams["SHOW_MENU"] == "N" ? "N" : "Y" );
$arParams["ALLOW_EMPTY_CITY"] = ($arParams["CITY_OUT_LOCATION"] == "N" ? "N" : "Y" );
$arParams["SHOW_AJAX_LOCATIONS"] = $arParams["SHOW_AJAX_LOCATIONS"] == 'N' ? 'N' : 'Y';
$arParams['PRICE_VAT_SHOW_VALUE'] = $arParams['PRICE_VAT_SHOW_VALUE'] == 'N' ? 'N' : 'Y';
$arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] = (($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y") ? "Y" : "N");
$arParams["SEND_NEW_USER_NOTIFY"] = (($arParams["SEND_NEW_USER_NOTIFY"] == "N") ? "N" : "Y");
$arResult["AUTH"]["new_user_registration_email_confirmation"] = ((COption::GetOptionString("main", "new_user_registration_email_confirmation", "N") == "Y") ? "Y" : "N");
$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");

$bUseAccountNumber = (COption::GetOptionString("sale", "account_number_template", "") !== "") ? true : false;

if (!$arParams["DELIVERY_NO_SESSION"])
	$arParams["DELIVERY_NO_SESSION"] = "N";

if($arParams["SET_TITLE"] == "Y")
{
	if($USER->IsAuthorized())
		$APPLICATION->SetTitle(GetMessage("STOF_MAKING_ORDER"));
	else
		$APPLICATION->SetTitle(GetMessage("STOF_AUTH"));
}
/*----------------------Установка параметров---END----------------*/


if(strlen($arResult["POST"]["ORDER_PRICE"])>0)
	$arResult["ORDER_PRICE"]  = doubleval($arResult["POST"]["ORDER_PRICE"]);
if(strlen($arResult["POST"]["ORDER_WEIGHT"])>0)
	$arResult["ORDER_WEIGHT"] = doubleval($arResult["POST"]["ORDER_WEIGHT"]);

$arResult["WEIGHT_UNIT"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_unit', "", SITE_ID));
$arResult["WEIGHT_KOEF"] = htmlspecialcharsbx(COption::GetOptionString('sale', 'weight_koef', 1, SITE_ID));

$GLOBALS['CATALOG_ONETIME_COUPONS_BASKET']=null;
$GLOBALS['CATALOG_ONETIME_COUPONS_ORDER']=null;

$allCurrency = CSaleLang::GetLangCurrency(SITE_ID);


/*-------------------------------------Заполняем arResult["POST"] из $_POST-----------------------------------------*/
if ($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
{
	foreach($_POST as $k => $v)
	{
		if(!is_array($v))
		{
			$arResult["POST"][$k] = htmlspecialcharsex($v);
			$arResult["POST"]['~'.$k] = $v;
		}
		else
		{
			foreach($v as $kk => $vv)
			{
				$arResult["POST"][$k][$kk] = htmlspecialcharsex($vv);
				$arResult["POST"]['~'.$k][$kk] = $vv;
			}
		}
	}
}
/*-------------------------------------Заполняем arResult["POST"] из $_POST----END--------------------------------*/


$arResult["SKIP_FIRST_STEP"] = (($arResult["POST"]["SKIP_FIRST_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_SECOND_STEP"] = (($arResult["POST"]["SKIP_SECOND_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_THIRD_STEP"] = (($arResult["POST"]["SKIP_THIRD_STEP"] == "Y") ? "Y" : "N");
$arResult["SKIP_FORTH_STEP"] = (($arResult["POST"]["SKIP_FORTH_STEP"] == "Y") ? "Y" : "N");

/*-------------------------------------Узнаю тип плательщика-----------------------------------------*/
$rsUser = CUser::GetByID($USER->GetID());
$arResult["USER"] = $rsUser->Fetch();

$arGroupUser = CUser::GetUserGroup($USER->GetID());
$arOptGroup = getOptGroup();
$optUserGroup = array_intersect($arOptGroup, $arGroupUser);


if (in_array(RETAIL_GROUP_ID, $arGroupUser)) $arResult["POST"]["PERSON_TYPE"] = RETAIL_BUYER_ID;
elseif (in_array(WANT_BE_OPT_GROUP_ID, $arGroupUser)) $arResult["POST"]["PERSON_TYPE"] = RETAIL_BUYER_ID;
elseif(count($optUserGroup)>0) $arResult["POST"]["PERSON_TYPE"] = WHOLESALE_BUYER_ID;
else $arResult["POST"]["PERSON_TYPE"] = RETAIL_BUYER_ID;


/*-------------------------------------Узнаю тип плательщика-------END------------------------------*/



/*-------------------------------------Заполнение $arResult из $arResult["POST"]------------------------------*/
if(strlen($arResult["POST"]["PERSON_TYPE"])>0)
	$arResult["PERSON_TYPE"] = IntVal($arResult["POST"]["PERSON_TYPE"]);

if(strlen($arResult["POST"]["PROFILE_ID"])>0)
{
	$arResult["PROFILE_ID"] = IntVal($arResult["POST"]["PROFILE_ID"]);
	$dbUserProfiles = CSaleOrderUserProps::GetList(
		array("DATE_UPDATE" => "DESC"),
		array(
			"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
			"USER_ID" => IntVal($USER->GetID()),
			"ID" => $arResult["PROFILE_ID"],
		)
	);
	if(!$dbUserProfiles->GetNext())
		$arResult["PROFILE_ID"] = 0;
}
if(strlen($arResult["POST"]["DELIVERY_ID"])>0)
{
	if (strpos($arResult["POST"]["DELIVERY_ID"], ":") === false)
		$arResult["DELIVERY_ID"] = IntVal($arResult["POST"]["DELIVERY_ID"]);
	else
		$arResult["DELIVERY_ID"] = explode(":", $arResult["POST"]["DELIVERY_ID"]);
}

if(strlen($arResult["POST"]["PAY_SYSTEM_ID"])>0)
	$arResult["PAY_SYSTEM_ID"] = IntVal($arResult["POST"]["PAY_SYSTEM_ID"]);

if(strlen($arResult["POST"]["PAY_CURRENT_ACCOUNT"])>0)
	$arResult["PAY_CURRENT_ACCOUNT"] = $arResult["POST"]["PAY_CURRENT_ACCOUNT"];
else
	$arResult["PAY_CURRENT_ACCOUNT"] = "N";

if(strlen($arResult["POST"]["TAX_EXEMPT"])>0)
	$arResult["TAX_EXEMPT"] = $arResult["POST"]["TAX_EXEMPT"];

if(strlen($arResult["POST"]["ORDER_DESCRIPTION"])>0)
	$arResult["ORDER_DESCRIPTION"] = trim($arResult["POST"]["ORDER_DESCRIPTION"]);
/*-------------------------------------Заполнение $arResult из $arResult["POST"]---END--------------------*/



if ($_REQUEST["CurrentStep"] == 5 || ($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid())))
{
	if(strlen($_REQUEST["ORDER_ID"])>0)
		$ID = urldecode(urldecode($_REQUEST["ORDER_ID"]));
	if(IntVal($_REQUEST["CurrentStep"])>0)
		$arResult["CurrentStep"] = IntVal($_REQUEST["CurrentStep"]);
	if(IntVal($_REQUEST["CurrentStep"])>0)
		$CurrentStepTmp = IntVal($_REQUEST["CurrentStep"]);
	elseif(IntVal($arResult["POST"]["CurrentStep"])>0)
		$CurrentStepTmp = IntVal($arResult["POST"]["CurrentStep"]);
}

$arResult["BACK"] = (($arResult["POST"]["BACK"] == "Y") ? "Y" : "");

if ($_SERVER["REQUEST_METHOD"] == "POST" && strlen($_REQUEST["backButton"]) > 0 && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
{
	if($arResult["POST"]["CurrentStep"] == 6 && $arResult["SKIP_FORTH_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 3;

	if($arResult["POST"]["CurrentStepTmp"] <= 5 && $arResult["SKIP_THIRD_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 2;

	if($arResult["POST"]["CurrentStepTmp"] <= 3 && $arResult["SKIP_SECOND_STEP"] == "Y")
		$arResult["CurrentStepTmp"] = 1;
	
	if(IntVal($arResult["CurrentStepTmp"])>0)
		$arResult["CurrentStep"] = $arResult["CurrentStepTmp"];
	else
		$arResult["CurrentStep"] = $arResult["CurrentStep"] - 2;
	
	$arResult["BACK"] = "Y";
}
if ($arResult["CurrentStep"] <= 0)
	$arResult["CurrentStep"] = 1;
$arResult["ERROR_MESSAGE"] = "";

/*******************************************************************************/
/*****************  ACTION  ****************************************************/
/*******************************************************************************/

if ($arResult["CurrentStep"] == 1) //шаг выбора типа платильщика
{
	$arResult["CurrentStep"]++;
}

if (false)
{
	$arResult["USER_LOGIN"] = ((strlen($arResult["POST"]["USER_LOGIN"]) > 0) ? $arResult["POST"]["USER_LOGIN"] : htmlspecialcharsbx(${COption::GetOptionString("main", "cookie_name", "BITRIX_SM")."_LOGIN"}));
	$arResult["AUTH"]["captcha_registration"] = ((COption::GetOptionString("main", "captcha_registration", "N") == "Y") ? "Y" : "N");
	if($arResult["AUTH"]["captcha_registration"] == "Y")
		$arResult["AUTH"]["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
	$arResult["AUTH"]["new_user_registration"] = ((COption::GetOptionString("main", "new_user_registration", "Y") == "Y") ? "Y" : "N");

	if($_SERVER["REQUEST_METHOD"] == "POST" && ($arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid()))
	{
		if ($arResult["POST"]["do_authorize"] == "Y")
		{
			if (strlen($arResult["POST"]["USER_LOGIN"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH_LOGIN").".<br />";

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$arAuthResult = $USER->Login($arResult["POST"]["~USER_LOGIN"], $arResult["POST"]["~USER_PASSWORD"], "N");
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_AUTH").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
				else
					LocalRedirect($arParams["PATH_TO_ORDER"]);

			}
		}
		elseif ($arResult["POST"]["do_register"] == "Y" && $arResult["AUTH"]["new_user_registration"] == "Y")
		{
			if (strlen($arResult["POST"]["NEW_NAME"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_NAME").".<br />";

			if (strlen($arResult["POST"]["NEW_LAST_NAME"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_LASTNAME").".<br />";

			if (strlen($arResult["POST"]["NEW_EMAIL"]) <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_EMAIL").".<br />";
			elseif (!check_email($arResult["POST"]["NEW_EMAIL"]))
				$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_BAD_EMAIL").".<br />";

			if ($arResult["POST"]["NEW_GENERATE"] == "Y")
			{
				$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];

				$pos = strpos($arResult["POST"]["~NEW_LOGIN"], "@");
				if ($pos !== false)
					$arResult["POST"]["~NEW_LOGIN"] = substr($arResult["POST"]["~NEW_LOGIN"], 0, $pos);

				if (strlen($arResult["POST"]["~NEW_LOGIN"]) > 47)
					$arResult["POST"]["~NEW_LOGIN"] = substr($arResult["POST"]["~NEW_LOGIN"], 0, 47);

				if (strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
					$arResult["POST"]["~NEW_LOGIN"] .= "_";

				if (strlen($arResult["POST"]["~NEW_LOGIN"]) < 3)
					$arResult["POST"]["~NEW_LOGIN"] .= "_";

				$dbUserLogin = CUser::GetByLogin($arResult["POST"]["~NEW_LOGIN"]);
				if ($arUserLogin = $dbUserLogin->Fetch())
				{
					$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
					$uind = 0;
					do
					{
						$uind++;
						if ($uind == 10)
						{
							$arResult["POST"]["~NEW_LOGIN"] = $arResult["POST"]["~NEW_EMAIL"];
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
						}
						elseif ($uind > 10)
						{
							$arResult["POST"]["~NEW_LOGIN"] = "buyer".time().GetRandomCode(2);
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"];
							break;
						}
						else
						{
							$newLoginTmp = $arResult["POST"]["~NEW_LOGIN"].$uind;
						}
						$dbUserLogin = CUser::GetByLogin($newLoginTmp);
					}
					while ($arUserLogin = $dbUserLogin->Fetch());
					$arResult["POST"]["~NEW_LOGIN"] = $newLoginTmp;
				}

				$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
				if($def_group!="")
				{
					$GROUP_ID = explode(",", $def_group);
					$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
				}
				else
				{
					$arPolicy = $USER->GetGroupPolicy(array());
				}

				$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
				if($password_min_length <= 0)
					$password_min_length = 6;
				$password_chars = array(
					"abcdefghijklnmopqrstuvwxyz",
					"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
					"0123456789",
				);
				if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
					$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
				$arResult["POST"]["~NEW_PASSWORD"] = $arResult["POST"]["~NEW_PASSWORD_CONFIRM"] = randString($password_min_length, $password_chars);
			}
			else
			{
				if (strlen($arResult["POST"]["NEW_LOGIN"]) <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG").".<br />";

				if (strlen($arResult["POST"]["NEW_PASSWORD"]) <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

				if (strlen($arResult["POST"]["NEW_PASSWORD"]) > 0 && strlen($arResult["POST"]["NEW_PASSWORD_CONFIRM"]) <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_FLAG1").".<br />";

				if (strlen($arResult["POST"]["NEW_PASSWORD"]) > 0
					&& strlen($arResult["POST"]["NEW_PASSWORD_CONFIRM"]) > 0
					&& $arResult["POST"]["NEW_PASSWORD"] != $arResult["POST"]["NEW_PASSWORD_CONFIRM"])
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_PASS").".<br />";
			}

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$arAuthResult = $USER->Register($arResult["POST"]["~NEW_LOGIN"], $arResult["POST"]["~NEW_NAME"], $arResult["POST"]["~NEW_LAST_NAME"], $arResult["POST"]["~NEW_PASSWORD"], $arResult["POST"]["~NEW_PASSWORD_CONFIRM"], $arResult["POST"]["~NEW_EMAIL"], LANG, $arResult["POST"]["~captcha_word"], $arResult["POST"]["~captcha_sid"]);
				if ($arAuthResult != False && $arAuthResult["TYPE"] == "ERROR")
					$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG").((strlen($arAuthResult["MESSAGE"]) > 0) ? ": ".$arAuthResult["MESSAGE"] : ".<br />" );
				else
				{
					if ($USER->IsAuthorized())
					{
						if($arParams["SEND_NEW_USER_NOTIFY"] == "Y")
							CUser::SendUserInfo($USER->GetID(), SITE_ID, GetMessage("INFO_REQ"), true);
						LocalRedirect($arParams["PATH_TO_ORDER"]);
					}
					else
					{
						$arResult["ERROR_MESSAGE"] .= GetMessage("STOF_ERROR_REG_CONFIRM")."<br />";
					}
				}
			}
		}
	}
}
else
{
	$arResult["BASE_LANG_CURRENCY"] = CSaleLang::GetLangCurrency(SITE_ID);

	if ($arResult["CurrentStep"] > 0 && $arResult["CurrentStep"] <= 6)
	{
		if ($arResult["PAY_CURRENT_ACCOUNT"] != "N" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
			$arResult["PAY_CURRENT_ACCOUNT"] = "Y";

		// <***************** BEFORE 1 STEP
		$arResult["ORDER_PRICE"] = 0;
		$arResult["ORDER_WEIGHT"] = 0;
		$bProductsInBasket = False;
		$arResult["bUsingVat"] = "N";
		$arResult["vatRate"] = 0;
		$arResult["vatSum"] = 0;
		$arProductsInBasket = array();
		$DISCOUNT_PRICE_ALL = 0;
		CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
		$dbBasketItems = CSaleBasket::GetList(
			array("ID" => "ASC"),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL"
			),
			false,
			false,
			array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME", "DISCOUNT_PRICE", "VAT_RATE")
		);
		while ($arBasketItems = $dbBasketItems->GetNext())
		{
			if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
			{
				$arBasketItems["PRICE"] = roundEx($arBasketItems["PRICE"], SALE_VALUE_PRECISION);
				$arBasketItems["QUANTITY"] = DoubleVal($arBasketItems["QUANTITY"]);
				$arBasketItems["WEIGHT"] = DoubleVal($arBasketItems["WEIGHT"]);
				$arBasketItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arBasketItems["WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
				$arBasketItems["VAT_RATE"] = DoubleVal($arBasketItems["VAT_RATE"]);
				//$arBasketItems["DISCOUNT_PRICE"] = roundEx($arBasketItems["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);

				$DISCOUNT_PRICE_ALL += $arBasketItems["DISCOUNT_PRICE"] * $arBasketItems["QUANTITY"];

				$arResult["ORDER_PRICE"] += $arBasketItems["PRICE"] * $arBasketItems["QUANTITY"];
				$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
				if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
				{

					$arResult["bUsingVat"] = "Y";
					if($arBasketItems["VAT_RATE"] > $arResult["vatRate"])
						$arResult["vatRate"] = $arBasketItems["VAT_RATE"];

					//$arBasketItems["VAT_VALUE"] = roundEx((($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]), SALE_VALUE_PRECISION);
					$arBasketItems["VAT_VALUE"] = (($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]);
					$arResult["vatSum"] += roundEx($arBasketItems["VAT_VALUE"] * $arBasketItems["QUANTITY"], SALE_VALUE_PRECISION);
				}
				$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);

				$arProductsInBasket[] = $arBasketItems;
				$bProductsInBasket = true;
			}
		}

		if (!$bProductsInBasket)
		{
			LocalRedirect($arParams["PATH_TO_BASKET"]);
			$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_BASKET_EMPTY");
		}

		// DISCOUNT
		$countProdInBaket = count($arProductsInBasket);
		for ($i = 0; $i < $countProdInBaket; $i++)
			$arProductsInBasket[$i]["DISCOUNT_PRICE"] = DoubleVal($arProductsInBasket[$i]["PRICE"]);

		$arMinDiscount = array();
		$allSum = 0;
		foreach ($arProductsInBasket as &$arResultItem)
		{
			$allSum += ($arResultItem["PRICE"] * $arResultItem["QUANTITY"]);
		}
		$dblMinPrice = $allSum;

		$dbDiscount = CSaleDiscount::GetList(
			array("SORT" => "ASC"),
			array(
				"LID" => SITE_ID,
				"ACTIVE" => "Y",
				"!>ACTIVE_FROM" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"!<ACTIVE_TO" => Date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL"))),
				"<=PRICE_FROM" => $arResult["ORDER_PRICE"],
				">=PRICE_TO" => $arResult["ORDER_PRICE"],
				"USER_GROUPS" => $USER->GetUserGroupArray(),
			),
			false,
			false,
			array("*")
		);
		$arResult["DISCOUNT_PRICE"] = 0;
		$arResult["DISCOUNT_PERCENT"] = 0;
		$arDiscounts = array();

		while ($arDiscount = $dbDiscount->Fetch())
		{
			$dblDiscount = 0;
			$allSum_tmp = $allSum;

			if ($arDiscount["DISCOUNT_TYPE"] == "P")
			{
				if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
				{
					foreach ($arProductsInBasket as &$arBasketItem)
					{
						$curDiscount = roundEx($arBasketItem["PRICE"] * $arBasketItem["QUANTITY"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$dblDiscount += $curDiscount;
					}
				}
				else
				{
					foreach ($arProductsInBasket as &$arBasketItem)
					{
						$curDiscount = roundEx($arBasketItem["PRICE"] * $arDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$dblDiscount += $curDiscount * $arBasketItem["QUANTITY"];
					}
				}
			}
			else
			{
				$dblDiscount = roundEx(CCurrencyRates::ConvertCurrency($arDiscount["DISCOUNT_VALUE"], $arDiscount["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}

			$allSum = $allSum - $dblDiscount;
			if ($dblMinPrice > $allSum)
			{
				$dblMinPrice = $allSum;
				$arMinDiscount = $arDiscount;
			}
			$allSum = $allSum_tmp;
		}

		if (!empty($arMinDiscount))
		{
			if ($arMinDiscount["DISCOUNT_TYPE"] == "P")
			{
				$arResult["DISCOUNT_PERCENT"] = $arMinDiscount["DISCOUNT_VALUE"];
				$countProdBasket = count($arProductsInBasket);
				for ($bi = 0; $bi < $countProdBasket; $bi++)
				{
					if($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y")
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arProductsInBasket[$bi]["QUANTITY"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arResult["DISCOUNT_PRICE"] += $curDiscount;
					}
					else
					{
						$curDiscount = roundEx($arProductsInBasket[$bi]["PRICE"] * $arMinDiscount["DISCOUNT_VALUE"] / 100, SALE_VALUE_PRECISION);
						$arResult["DISCOUNT_PRICE"] += $curDiscount * $arProductsInBasket[$bi]["QUANTITY"];
					}
					$arProductsInBasket[$bi]["DISCOUNT_PRICE"] = $arProductsInBasket[$bi]["PRICE"] - $curDiscount;
				}
			}
			else
			{
				$arResult["DISCOUNT_PRICE"] = CCurrencyRates::ConvertCurrency($arMinDiscount["DISCOUNT_VALUE"], $arMinDiscount["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]);
				$arResult["DISCOUNT_PRICE"] = roundEx($arResult["DISCOUNT_PRICE"], SALE_VALUE_PRECISION);
			}
		}
		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 1)
		{
			// <***************** AFTER 1 STEP
			if ($arResult["PERSON_TYPE"] <= 0)
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PERS_TYPE")."<br />";

			if (($arResult["PERSON_TYPE"] > 0) && !($arPersType = CSalePersonType::GetByID($arResult["PERSON_TYPE"])))
				$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PERS_TYPE_NOT_FOUND")."<br />";

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 1;
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 2)
		{
			// <***************** AFTER 2 STEP
			if ($arResult["PROFILE_ID"] > 0 && $USER->IsAuthorized())
			{
				$dbUserProps = CSaleOrderUserPropsValue::GetList(
					array("SORT" => "ASC"),
					array("USER_PROPS_ID" => $arResult["PROFILE_ID"]),
					false,
					false,
					array("ID", "ORDER_PROPS_ID", "VALUE", "SORT")
				);
				while ($arUserProps = $dbUserProps->GetNext())
				{
					$arResult["POST"]["ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["VALUE"];
					$arResult["POST"]["~ORDER_PROP_".$arUserProps["ORDER_PROPS_ID"]] = $arUserProps["~VALUE"];
				}
			}

			$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
			if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
				$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

			$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				$arFilter,
				false,
				false,
				array("ID", "NAME", "TYPE", "IS_LOCATION", "IS_LOCATION4TAX", "IS_PROFILE_NAME", "IS_PAYER", "IS_EMAIL", "IS_ZIP", "REQUIED", "SORT")
			);
			while ($arOrderProps = $dbOrderProps->GetNext())
			{

				$bErrorField = False;
				$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProps["ID"]];

				if ($arOrderProps["TYPE"]=="LOCATION")
				{
					if (isset($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]) && intval($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]) > 0)
					{
						$curVal = intval($arResult["POST"]["NEW_LOCATION_".$arOrderProps["ID"]]);
						$arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]] = $curVal;
					}
				}
				if ($arOrderProps["TYPE"]=="LOCATION" && ($arOrderProps["IS_LOCATION"]=="Y" || $arOrderProps["IS_LOCATION4TAX"]=="Y"))
				{
					if ($arOrderProps["IS_LOCATION"]=="Y")
						$arResult["DELIVERY_LOCATION"] = IntVal($curVal);
					if ($arOrderProps["IS_LOCATION4TAX"]=="Y")
						$arResult["TAX_LOCATION"] = IntVal($curVal);

					if (IntVal($curVal)<=0) $bErrorField = True;
				}
				elseif ($arOrderProps["IS_PROFILE_NAME"]=="Y" || $arOrderProps["IS_PAYER"]=="Y" || $arOrderProps["IS_EMAIL"]=="Y" || $arOrderProps["IS_ZIP"]=="Y")
				{
					if ($arOrderProps["IS_PROFILE_NAME"]=="Y")
					{
						$arResult["PROFILE_NAME"] = Trim($curVal);
						if (strlen($arResult["PROFILE_NAME"])<=0)
							$bErrorField = True;
					}
					if ($arOrderProps["IS_PAYER"]=="Y")
					{
						$arResult["PAYER_NAME"] = Trim($curVal);
						if (strlen($arResult["PAYER_NAME"])<=0)
							$bErrorField = True;
					}
					if ($arOrderProps["IS_EMAIL"]=="Y")
					{
						$arResult["USER_EMAIL"] = Trim($curVal);
						if (strlen($arResult["USER_EMAIL"])<=0 || !check_email($arResult["USER_EMAIL"]))
							$bErrorField = True;
					}
					if($arOrderProps["IS_ZIP"]=="Y")
					{
						$arResult["DELIVERY_LOCATION_ZIP"] = $curVal;
						if (strlen($arResult["DELIVERY_LOCATION_ZIP"])<=0)
							$bErrorField = True;
					}
				}
				elseif ($arOrderProps["REQUIED"]=="Y")
				{
					if ($arOrderProps["TYPE"]=="TEXT" || $arOrderProps["TYPE"]=="TEXTAREA" || $arOrderProps["TYPE"]=="RADIO" || $arOrderProps["TYPE"]=="SELECT" || $arOrderProps["TYPE"] == "CHECKBOX")
					{
						if (strlen($curVal)<=0)
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="LOCATION")
					{
						if (IntVal($curVal)<=0)
							$bErrorField = True;
					}
					elseif ($arOrderProps["TYPE"]=="MULTISELECT")
					{
						if (!is_array($curVal) || count($curVal)<=0)
							$bErrorField = True;
					}
				}

				if ($bErrorField)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_EMPTY_FIELD")." \"".$arOrderProps["NAME"]."\".<br />";
			}
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 2;
		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] > 3)
		{
			// <***************** AFTER 3 STEP
			$arResult["TaxExempt"] = array();
			$arUserGroups = $USER->GetUserGroupArray();

			if($arResult["bUsingVat"] != "Y")
			{
				$dbTaxExemptList = CSaleTax::GetExemptList(array("GROUP_ID" => $arUserGroups));
				while ($TaxExemptList = $dbTaxExemptList->Fetch())
				{
					if (!in_array(IntVal($TaxExemptList["TAX_ID"]), $arResult["TaxExempt"]))
					{
						$arResult["TaxExempt"][] = IntVal($TaxExemptList["TAX_ID"]);
					}
				}
			}

			// DELIVERY

			$arResult["DELIVERY_PRICE"] = 0;
			if (is_array($arResult["DELIVERY_ID"]))
			{
				$locFrom = COption::GetOptionString('sale', 'location');
				if(CSaleLocation::isLocationProMigrated())
					$locFrom = CSaleLocation::getLocationIDByCODE($locFrom);

				$arOrder = array(
					"PRICE" => $arResult["ORDER_PRICE"],
					"WEIGHT" => $arResult["ORDER_WEIGHT"],
					"LOCATION_FROM" => $locFrom,
					"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
					"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
				);

				$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrder, $arResult["BASE_LANG_CURRENCY"]);

				if ($arDeliveryPrice["RESULT"] == "ERROR")
					$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
				else
					$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);
			}
			else
			{
				if (($arResult["DELIVERY_ID"] > 0) && !($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_DELIVERY_NOT_FOUND")."<br />";
				elseif (($arResult["DELIVERY_ID"] > 0) && $arDeliv)
					$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
			}

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 3;
		}

		// TAX
		$arResult["TAX_EXEMPT"] = (($_REQUEST["TAX_EXEMPT"]=="Y") ? "Y" : "N");
		if ($arResult["TAX_EXEMPT"] == "N")
		{
			unset($arResult["TaxExempt"]);
			$arResult["TaxExempt"] = array();
		}


		$arResult["TAX_PRICE"] = 0;
		$arResult["arTaxList"] = array();
		if($arResult["bUsingVat"] != "Y")
		{
			$dbTaxRate = CSaleTaxRate::GetList(
				array("APPLY_ORDER"=>"ASC"),
				array(
					"LID" => SITE_ID,
					"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
					"ACTIVE" => "Y",
					"LOCATION" => IntVal($arResult["TAX_LOCATION"])
				)
			);
			while ($arTaxRate = $dbTaxRate->GetNext())
			{
				if (!in_array(IntVal($arTaxRate["TAX_ID"]), $arResult["TaxExempt"]))
				{
					$arResult["arTaxList"][] = $arTaxRate;
				}
			}

			$arTaxSums = array();
			if (count($arResult["arTaxList"]) > 0)
			{
				$countProdBasket = count($arProductsInBasket);
				for ($i = 0; $i < $countProdBasket; $i++)
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
						$arProductsInBasket[$i]["DISCOUNT_PRICE"] * $arProductsInBasket[$i]["QUANTITY"],
						$arResult["arTaxList"],
						$arResult["BASE_LANG_CURRENCY"]
					);

					$countResultTax = count($arResult["arTaxList"]);
					for ($j = 0; $j < $countResultTax; $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}
				if(DoubleVal($arResult["DELIVERY_PRICE"])>0 && $arParams["COUNT_DELIVERY_TAX"] == "Y")
				{
					$arResult["TAX_PRICE_tmp"] = CSaleOrderTax::CountTaxes(
						$arResult["DELIVERY_PRICE"],
						$arResult["arTaxList"],
						$arResult["BASE_LANG_CURRENCY"]
					);

					$countResTax = count($arResult["arTaxList"]);
					for ($j = 0; $j < $countResTax; $j++)
					{
						$arResult["arTaxList"][$j]["VALUE_MONEY"] += $arResult["arTaxList"][$j]["TAX_VAL"];
					}
				}

				$countResultTax = count($arResult["arTaxList"]);
				for ($i = 0; $i < $countResultTax; $i++)
				{
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["VALUE"] = $arResult["arTaxList"][$i]["VALUE_MONEY"];
					$arTaxSums[$arResult["arTaxList"][$i]["TAX_ID"]]["NAME"] = $arResult["arTaxList"][$i]["NAME"];
					if ($arResult["arTaxList"][$i]["IS_IN_PRICE"] != "Y")
					{
						$arResult["TAX_PRICE"] += $arResult["arTaxList"][$i]["VALUE_MONEY"];
					}
				}
			}
		}
		else
		{
			if(DoubleVal($arResult["DELIVERY_PRICE"])>0 && $arParams["COUNT_DELIVERY_TAX"] == "Y")
				$arResult["vatSum"] += roundEx($arResult["DELIVERY_PRICE"] * $arResult["vatRate"] / (1 + $arResult["vatRate"]), 2);

			$arResult["arTaxList"][] = Array(
				"NAME" => GetMessage("STOF_VAT"),
				"IS_PERCENT" => "Y",
				"VALUE" => $arResult["vatRate"]*100,
				"VALUE_MONEY" => $arResult["vatSum"],
				"APPLY_ORDER" => 100,
				"IS_IN_PRICE" => "Y",
				"CODE" => "VAT"
			);

		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] >= 4)
		{
			// <***************** AFTER 4 STEP
			// PAY_SYSTEM
			if($arResult["CurrentStep"] > 4)
			{
				$arResult["PAY_SYSTEM_ID"] = IntVal($_REQUEST["PAY_SYSTEM_ID"]);
				if ($arResult["PAY_SYSTEM_ID"] <= 0)
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_NO_PAY_SYS")."<br />";
				if (($arResult["PAY_SYSTEM_ID"] > 0) && !($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
					$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_PAY_SYS_NOT_FOUND")."<br />";
			}
			//if ($arResult["PAY_CURRENT_ACCOUNT"] != "Y")
			//$arResult["PAY_CURRENT_ACCOUNT"] = "N";

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 4;

		}

		if (strlen($arResult["ERROR_MESSAGE"]) <= 0 && $arResult["CurrentStep"] >= 5)
		{
			// <***************** AFTER 5 STEP
			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 5;

			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
				if($USER->IsAuthorized())
				{
					$USER_ID = IntVal($USER->GetID());
					// перенесем данные из формы оформления заказа, если их еще нет в профиле пользователя
					if(intval($arResult['POST']['PERSON_TYPE'])) // только если определен тип пательщика, иначе не узнаем,какие свойства нужны
					{
						$arUserFilter = Array("ID"=> $USER->GetID());
						$arUserSelect = array(
							"LAST_NAME", "SECOND_NAME"
						);
						$userOrder = "desc";
						$userBy = "personal_country";
						$dbUserData = CUser::GetList($userBy,$userOrder, $arUserFilter,$arUserSelect);
						if($arUserData = $dbUserData->GetNext())
						{
							$arResult['USER_DATA'] = $arUserData;
						}
						if(strlen($arResult['USER_DATA']['LAST_NAME'])==0 || strlen($arResult['USER_DATA']['SECOND_NAME'])==0)
						{
							// получаем свойства "Фамилия" и "Отчество" для данного типа плательщика
							$orderProps = array();
							$arSortOrderProps = array('SORT'=>'ASC');
							$arFilterOrderProps = array(
								'PERSON_TYPE_ID'=>$arResult['POST']['PERSON_TYPE'],
								'CODE' =>array('SURNAME','MIDDLE_NAME')
							);
							$arSelectOrderProps = array('ID','NAME','CODE');
							$rsOrderProps = CSaleOrderProps::GetList($arSortOrderProps,$arFilterOrderProps,false,false,$arSelectOrderProps);
							if($rsOrderProps->SelectedRowsCount())
							{
								while($arProps = $rsOrderProps->GetNext())
								{
									if($arProps['CODE']=="SURNAME")
									{
										$orderProps['LAST_NAME'] = $arProps['ID'];
									}
									elseif($arProps['CODE']=="MIDDLE_NAME")
									{
										$orderProps['SECOND_NAME'] = $arProps['ID'];
									}
								}
							}
							if(!empty($orderProps))
							{
								$arUserFields = array();
								if(strlen($arResult['USER_DATA']['LAST_NAME'])==0 &&
									strlen($arResult["POST"]["ORDER_PROP_".$orderProps['LAST_NAME']]))
								{
									$arUserFields['LAST_NAME'] = $arResult["POST"]["ORDER_PROP_".$orderProps['LAST_NAME']];
								}
								if(strlen($arResult['USER_DATA']['SECOND_NAME'])==0 &&
									strlen($arResult["POST"]["ORDER_PROP_".$orderProps['SECOND_NAME']]))
								{
									$arUserFields['SECOND_NAME'] = $arResult["POST"]["ORDER_PROP_".$orderProps['SECOND_NAME']];
								}
								if(!empty($arUserFields))
								{
									$user = new CUser;
									$user->Update($USER_ID,$arUserFields);
								}
							}
						}
					}
				}
				else {

					if (!\Bitrix\Main\Loader::includeModule("iblock")) {
						$this->AbortResultCache();
						ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
						return;
					}

					$conformCode = randString(10);
					$password = randString(10);

					$login = substr($arResult["POST"]["ORDER_PROP_40"],0,strpos($arResult["POST"]["ORDER_PROP_40"],"@"));


					$rsUsers = CUser::GetList();
					$arUsersLogin = array();
					while($arPropUser = $rsUsers->Fetch()) {

						$arUsersLogin[] = $arPropUser["LOGIN"];
					};

					if(in_array($login,$arUsersLogin)){

						$counter = 0;
						while(in_array($login.strval($counter),$arUsersLogin)) {
							$counter++;
						}
						$login .= strval($counter);
					}


					//Добавление пользователя
					$user = new CUser;
					$arFields = Array(
						"NAME"              => $arResult["POST"]["ORDER_PROP_36"],
						"LAST_NAME"         => $arResult["POST"]["ORDER_PROP_37"],
						"SECOND_NAME"		=> $arResult["POST"]["ORDER_PROP_38"],
						"EMAIL"             => $arResult["POST"]["ORDER_PROP_40"],
						"LOGIN"             => $login,
						"PERSONAL_PHONE"	=> $arResult["POST"]["ORDER_PROP_39"],
						"LID" 				=> PROMOBILE_SITE_ID,
						"UF_CONFIRM_CODE"  => $conformCode,
						"PASSWORD"          => $password,
						"CONFIRM_PASSWORD"  => $password,
						"UF_DISCOUNT" => 0
					);

					unset($_SESSION["OPT_BUYER"]);
					$USER_ID = $user->Add($arFields);

					$arEventFields = array(
						"USER_NAME" => $arResult["POST"]["ORDER_PROP_36"],
						"USER_SURNAME" => $arResult["POST"]["ORDER_PROP_37"],
						"USER_EMAIL" =>$arResult["POST"]["ORDER_PROP_40"],
						"USER_LOGIN" => $login,
						"CONFIRM_CODE" => $conformCode,
						"USER_ID" => $USER_ID,
						"CREATE_PASSWORD_ADDRESS" => CREATE_PASSWORD_ADDRESS
					);
					CEvent::Send("CONFIRM_REGISTRATION", PROMOBILE_SITE_ID, $arEventFields);
				}
				$arFields = array(
					"LID" => SITE_ID,
					"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
					"PAYED" => "N",
					"CANCELED" => "N",
					"STATUS_ID" => "N",
					"PRICE" => $totalOrderPrice,
					"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
					"USER_ID" => $USER_ID,
					"PAY_SYSTEM_ID" => $arResult["PAY_SYSTEM_ID"],
					"PRICE_DELIVERY" => $arResult["DELIVERY_PRICE"],
					"DELIVERY_ID" => is_array($arResult["DELIVERY_ID"]) ? implode(":", $arResult["DELIVERY_ID"]) : ($arResult["DELIVERY_ID"] > 0 ? $arResult["DELIVERY_ID"] : false),
					"DISCOUNT_VALUE" => $arResult["DISCOUNT_PRICE"],
					"TAX_VALUE" => $arResult["bUsingVat"] == "Y" ? $arResult["vatSum"] : $arResult["TAX_PRICE"],
					"USER_DESCRIPTION" => $_POST['DELIVERY_INFO'],
					"ADDITIONAL_INFO" => $_POST['DELIVERY_INFO'],
					"COMMENTS" => $_POST['DELIVERY_INFO']
				);

				// add Guest ID
				if (CModule::IncludeModule("statistic"))
					$arFields["STAT_GID"] = CStatistic::GetEventParam();

				$affiliateID = CSaleAffiliate::GetAffiliate();
				if ($affiliateID > 0)
				{
					$dbAffiliat = CSaleAffiliate::GetList(array(), array("SITE_ID" => SITE_ID, "ID" => $affiliateID));
					$arAffiliates = $dbAffiliat->Fetch();
					if (count($arAffiliates) > 1)
						$arFields["AFFILIATE_ID"] = $affiliateID;
				}
				else
					$arFields["AFFILIATE_ID"] = false;

				$arResult["ORDER_ID"] = CSaleOrder::Add($arFields);
				$arResult["ORDER_ID"] = IntVal($arResult["ORDER_ID"]);

				if ($arResult["ORDER_ID"] <= 0)
				{
					if($ex = $APPLICATION->GetException())
						$arResult["ERROR_MESSAGE"] .= $ex->GetString();
					else
						$arResult["ERROR_MESSAGE"] .= GetMessage("SALE_ERROR_ADD_ORDER")."<br />";
				}
				else
				{
					$arOrder = CSaleOrder::GetByID($arResult["ORDER_ID"]);
				}

			}
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				CSaleBasket::OrderBasket($arResult["ORDER_ID"], CSaleBasket::GetBasketUserID(), SITE_ID, false);

				$dbBasketItems = CSaleBasket::GetList(
					array("ID" => "ASC"),
					array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"ORDER_ID" => $arResult["ORDER_ID"]
					),
					false,
					false,
					array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "NAME")
				);
				$arResult["ORDER_PRICE"] = 0;
				while ($arBasketItems = $dbBasketItems->GetNext())
				{
					$arResult["ORDER_PRICE"] += DoubleVal($arBasketItems["PRICE"]) * DoubleVal($arBasketItems["QUANTITY"]);
				}

				$totalOrderPrice = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
				CSaleOrder::Update($arResult["ORDER_ID"], Array("PRICE" => $totalOrderPrice));
			}
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				//if($arResult["bUsingVat"] != "Y")
				//{
				$countResultTax = count($arResult["arTaxList"]);
				for ($i = 0; $i < $countResultTax; $i++)
				{
					$arFields = array(
						"ORDER_ID" => $arResult["ORDER_ID"],
						"TAX_NAME" => $arResult["arTaxList"][$i]["NAME"],
						"IS_PERCENT" => $arResult["arTaxList"][$i]["IS_PERCENT"],
						"VALUE" => ($arResult["arTaxList"][$i]["IS_PERCENT"]=="Y") ? $arResult["arTaxList"][$i]["VALUE"] : RoundEx(CCurrencyRates::ConvertCurrency($arResult["arTaxList"][$i]["VALUE"], $arResult["arTaxList"][$i]["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION),
						"VALUE_MONEY" => $arResult["arTaxList"][$i]["VALUE_MONEY"],
						"APPLY_ORDER" => $arResult["arTaxList"][$i]["APPLY_ORDER"],
						"IS_IN_PRICE" => $arResult["arTaxList"][$i]["IS_IN_PRICE"],
						"CODE" => $arResult["arTaxList"][$i]["CODE"]
					);
					CSaleOrderTax::Add($arFields);
				}
				//}
				/*
				elseif($arResult["vatRate"] > 0)
				{
					$arFields = array(
							"ORDER_ID" => $arResult["ORDER_ID"],
							"TAX_NAME" => GetMessage("STOF_VAT"),
							"IS_PERCENT" => "Y",
							"VALUE" => $arResult["vatRate"],
							"VALUE_MONEY" => $arResult["vatSum"],
							"APPLY_ORDER" => 100,
							"IS_IN_PRICE" => "Y",
							"CODE" => "VAT"
						);
					CSaleOrderTax::Add($arFields);

				}
				*/
				$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
				if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
					$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

				$dbOrderProperties = CSaleOrderProps::GetList(
					array("SORT" => "ASC"),
					$arFilter,
					false,
					false,
					array("ID", "TYPE", "NAME", "CODE", "USER_PROPS", "SORT")
				);
				while ($arOrderProperties = $dbOrderProperties->Fetch())
				{
					$curVal = $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]];
					if ($arOrderProperties["TYPE"] == "MULTISELECT")
					{
						$curVal = "";
						$countResProp = count($arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]]);
						for ($i = 0; $i < $countResProp; $i++)
						{
							if ($i > 0)
								$curVal .= ",";
							$curVal .= $arResult["POST"]["~ORDER_PROP_".$arOrderProperties["ID"]][$i];
						}
					}

					if ($arOrderProperties["TYPE"] == "CHECKBOX" && strlen($curVal) <= 0 && $arOrderProperties["REQUIED"] != "Y")
					{
						$curVal = "N";
					}

					if (strlen($curVal) > 0)
					{
						// map to CODE for location
						if(CSaleLocation::isLocationProMigrated() && $arOrderProperties['TYPE'] == 'LOCATION')
							$curVal = CSaleLocation::getLocationCodeById($curVal);

						$arFields = array(
							"ORDER_ID" => $arResult["ORDER_ID"],
							"ORDER_PROPS_ID" => $arOrderProperties["ID"],
							"NAME" => $arOrderProperties["NAME"],
							"CODE" => $arOrderProperties["CODE"],
							"VALUE" => $curVal
						);
						CSaleOrderPropsValue::Add($arFields);
						if ( $arOrderProperties["USER_PROPS"] == "Y" && IntVal($arResult["PROFILE_ID"]) <= 0 && IntVal($arResult["USER_PROPS_ID"])<=0)
						{
							if (strlen($arResult["PROFILE_NAME"]) <= 0)
								$arResult["PROFILE_NAME"] = GetMessage("SALE_PROFILE_NAME")." ".Date("Y-m-d");

							$arFields = array(
								"NAME" => $arResult["PROFILE_NAME"],
								"USER_ID" => IntVal($USER->GetID()),
								"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]
							);
							$arResult["USER_PROPS_ID"] = CSaleOrderUserProps::Add($arFields);
							$arResult["USER_PROPS_ID"] = IntVal($arResult["USER_PROPS_ID"]);
						}

						if (IntVal($arResult["PROFILE_ID"]) <= 0 && $arOrderProperties["USER_PROPS"] == "Y" && $arResult["USER_PROPS_ID"] > 0)
						{
							$arFields = array(
								"USER_PROPS_ID" => $arResult["USER_PROPS_ID"],
								"ORDER_PROPS_ID" => $arOrderProperties["ID"],
								"NAME" => $arOrderProperties["NAME"],
								"VALUE" => $curVal
							);
							CSaleOrderUserPropsValue::Add($arFields);
						}
					}
				}
			}

			$withdrawSum = 0.0;
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y" && $arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
				{
					$dbUserAccount = CSaleUserAccount::GetList(
						array(),
						array(
							"USER_ID" => $USER->GetID(),
							"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
						)
					);
					if ($arUserAccount = $dbUserAccount->GetNext())
					{
						if ($arUserAccount["CURRENT_BUDGET"] > 0)
						{
							if(($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y" && DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($totalOrderPrice)) || $arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] != "Y")
							{
								$withdrawSum = CSaleUserAccount::Withdraw(
									$USER->GetID(),
									$totalOrderPrice,
									$arResult["BASE_LANG_CURRENCY"],
									$arResult["ORDER_ID"]
								);

								if ($withdrawSum > 0)
								{
									$arFields = array(
										"SUM_PAID" => $withdrawSum,
										"USER_ID" => $USER->GetID()
									);

									CSaleOrder::Update($arResult["ORDER_ID"], $arFields);

									if ($withdrawSum == $totalOrderPrice)
										CSaleOrder::PayOrder($arResult["ORDER_ID"], "Y", False, False);
								}
							}
						}
					}
				}
			}
			// mail message
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				$strOrderList = "";
				$dbBasketItems = CSaleBasket::GetList(
					array("ID" => "ASC"),
					array("ORDER_ID" => $arResult["ORDER_ID"]),
					false,
					false,
					array("ID", "NAME", "QUANTITY")
				);
				while ($arBasketItems = $dbBasketItems->Fetch())
				{
					$strOrderList .= $arBasketItems["NAME"]." - ".$arBasketItems["QUANTITY"]." ".GetMessage("SALE_QUANTITY_UNIT");
					$strOrderList .= "\n";
				}

				
				/***************************************Получение адреса**************************************/
				$arResult["ADDRESS"] = "";
				// доставка транспортной компанией (опт)
				if($arResult["POST"]["ORDER_PROP_74"])
				{
					$arResult["ADDRESS"] = $arResult["POST"]["ORDER_PROP_74"];
				}
				// доставка до двери (розница)
				elseif($arResult["POST"]["ORDER_PROP_46"])
				{
					if(strlen($arResult["POST"]["ORDER_PROP_47"]))
					{
						$arResult['POST']['ORDER_PROP_47'] = "ул. ".$arResult['POST']['ORDER_PROP_47'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_48"]))
					{
						$arResult['POST']['ORDER_PROP_48'] = "д. ".$arResult['POST']['ORDER_PROP_48'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_49"]))
					{
						$arResult['POST']['ORDER_PROP_49'] = "стр. ".$arResult['POST']['ORDER_PROP_49'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_50"]))
					{
						$arResult['POST']['ORDER_PROP_50'] = "корп. ".$arResult['POST']['ORDER_PROP_50'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_51"]))
					{
						$arResult['POST']['ORDER_PROP_51'] = "кв. ".$arResult['POST']['ORDER_PROP_51'];
					}
					$arResult["ADDRESS"] = implode(
						", ",
						array(
							$arResult["POST"]["ORDER_PROP_46"],
							$arResult["POST"]["ORDER_PROP_47"],
							$arResult["POST"]["ORDER_PROP_48"],
							$arResult["POST"]["ORDER_PROP_49"],
							$arResult["POST"]["ORDER_PROP_50"],
							$arResult["POST"]["ORDER_PROP_51"]
						)
					);
				}
				// доставка до ПВЗ (розница)
				elseif($arResult["POST"]["ORDER_PROP_75"])
				{
					$arResult["ADDRESS"] = $arResult["POST"]["ORDER_PROP_75"];
				}
				//  доставка Почтой России (розница)
				elseif($arResult["POST"]["ORDER_PROP_59"])
				{
					if(strlen($arResult["POST"]["ORDER_PROP_61"]))
					{
						$arResult['POST']['ORDER_PROP_61'] = "ул. ".$arResult['POST']['ORDER_PROP_61'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_62"]))
					{
						$arResult['POST']['ORDER_PROP_62'] = "д. ".$arResult['POST']['ORDER_PROP_62'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_63"]))
					{
						$arResult['POST']['ORDER_PROP_63'] = "стр. ".$arResult['POST']['ORDER_PROP_63'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_64"]))
					{
						$arResult['POST']['ORDER_PROP_64'] = "корп. ".$arResult['POST']['ORDER_PROP_64'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_65"]))
					{
						$arResult['POST']['ORDER_PROP_65'] = "кв. ".$arResult['POST']['ORDER_PROP_65'];
					}
					$arResult["ADDRESS"] = implode(
						", ",
						array(
							$arResult["POST"]["ORDER_PROP_58"],
							$arResult["POST"]["ORDER_PROP_59"],
							$arResult["POST"]["ORDER_PROP_60"],
							$arResult["POST"]["ORDER_PROP_61"],
							$arResult["POST"]["ORDER_PROP_62"],
							$arResult["POST"]["ORDER_PROP_63"],
							$arResult["POST"]["ORDER_PROP_64"],
							$arResult["POST"]["ORDER_PROP_65"],
						)
					);
				}
				// доставка до двери (опт)
				elseif($arResult["POST"]["ORDER_PROP_52"])
				{
					if(strlen($arResult["POST"]["ORDER_PROP_53"]))
					{
						$arResult['POST']['ORDER_PROP_53'] = "ул. ".$arResult['POST']['ORDER_PROP_53'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_54"]))
					{
						$arResult['POST']['ORDER_PROP_54'] = "д. ".$arResult['POST']['ORDER_PROP_54'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_55"]))
					{
						$arResult['POST']['ORDER_PROP_55'] = "стр. ".$arResult['POST']['ORDER_PROP_55'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_56"]))
					{
						$arResult['POST']['ORDER_PROP_56'] = "корп. ".$arResult['POST']['ORDER_PROP_56'];
					}
					if(strlen($arResult["POST"]["ORDER_PROP_57"]))
					{
						$arResult['POST']['ORDER_PROP_57'] = "кв. ".$arResult['POST']['ORDER_PROP_57'];
					}
					$arResult["ADDRESS"] = implode(
						", ",
						array(
							$arResult["POST"]["ORDER_PROP_52"],
							$arResult["POST"]["ORDER_PROP_53"],
							$arResult["POST"]["ORDER_PROP_54"],
							$arResult["POST"]["ORDER_PROP_55"],
							$arResult["POST"]["ORDER_PROP_56"],
							$arResult["POST"]["ORDER_PROP_57"]
						)
					);
				}
				/********************************************************************************/
				$userName="";
				if(strlen($arResult["PAYER_NAME"]))
				{
					$userName=$arResult['PAYER_NAME'];
				}
				else
				{
					$by="id";$order="asc";
					$dbUser=CUser::GetList($by,$order,array("ID"=>$USER_ID));
					if($dbUser->SelectedRowsCount())
					{
						if($arUser=$dbUser->Fetch())
						{
							if(strlen($arUser['NAME']))
							{
								$userName.=$arUser['NAME'];
							}
							if(strlen($arUser['LAST_NAME']))
							{
								$userName.=" ".$arUser['LAST_NAME'];
							}
						}
					}
					else
					{
						$userName=$USER->GetFormattedName(false);
					}
				}
				$arFields = Array(
					"ORDER_ID" => $arOrder["ACCOUNT_NUMBER"],
					"ORDER_DATE" => Date($DB->DateFormatToPHP(CLang::GetDateFormat("SHORT", SITE_ID))),
					"ORDER_USER" => $userName,
					"PRICE" => SaleFormatCurrency($totalOrderPrice, $arResult["BASE_LANG_CURRENCY"]),
					"BCC" => COption::GetOptionString("sale", "order_email"),
					"EMAIL" => ($arResult["POST"]["ORDER_PROP_40"]) ? $arResult["POST"]["ORDER_PROP_40"] : $arResult["POST"]["ORDER_PROP_45"],
					"ORDER_LIST" => $strOrderList,
					"SALE_EMAIL" => COption::GetOptionString("sale", "order_email"),
					"ADDRESS" => $arResult["ADDRESS"],
					"USER_PHONE" =>($arResult["POST"]["ORDER_PROP_39"])? $arResult["POST"]["ORDER_PROP_39"]: $arResult['USER']['PERSONAL_PHONE'],
					"DELIVERY_INFO" => $_POST['DELIVERY_INFO']
				);
				if(intval($arResult['PAY_SYSTEM_ID']))
				{
					$dbPaySystemInfo=CSalePaySystem::GetList(array("SORT"=>"ASC"),array("ID"=>$arResult['PAY_SYSTEM_ID']),
						false,false,array("ID","NAME"));
					if($dbPaySystemInfo->SelectedRowsCount())
					{
						if($arPaySystemInfo=$dbPaySystemInfo->GetNext())
						{
							$arFields['PAY_SYSTEM']=$arPaySystemInfo['NAME'];
						}
					}
				}
				if(intval($arResult['DELIVERY_ID']))
				{
					$dbDeliveryInfo=CSaleDelivery::GetList(array("SORT"=>"ASC"),array("ID"=>$arResult['DELIVERY_ID']),
						false,false,array('ID','NAME'));
					if($dbDeliveryInfo->SelectedRowsCount())
					{
						if($arDeliveryInfo=$dbDeliveryInfo->GetNext())
						{
							$arFields['DELIVERY_SYSTEM']=$arDeliveryInfo['NAME'];
						}
					}
				}
				if(intval($arResult['ORDER_ID']))
				{
					$dbOrderProps=CSaleOrder::GetList(array("ID"=>"DESC"),array("ID"=>$arResult['ORDER_ID']),false,
						false,array("ID","PAYED"));
					if($dbOrderProps->SelectedRowsCount())
					{
						if($arOrderProps=$dbOrderProps->GetNext())
						{
							if($arOrderProps['PAYED']=="Y")
							{
								$arFields['PAY_STATUS']="Оплачен";
							}
							else
							{
								$arFields['PAY_STATUS']="Не оплачен";
							}
						}
					}
				}

				$eventName = "SALE_NEW_ORDER";
				$bSend = true;
				foreach(GetModuleEvents("sale", "OnOrderNewSendEmail", true) as $arEvent)
					if (ExecuteModuleEventEx($arEvent, Array($arResult["ORDER_ID"], &$eventName, &$arFields))===false)
						$bSend = false;

				if($bSend)
				{
					$event = new CEvent;
					$event->Send($eventName, SITE_ID, $arFields, "N");
				}

				CSaleMobileOrderPush::send("ORDER_CREATED", array("ORDER_ID" => $arFields["ORDER_ID"]));
			}
			if (strlen($arResult["ERROR_MESSAGE"]) <= 0)
			{
				//LocalRedirect($arParams["PATH_TO_ORDER"]."?CurrentStep=7&ORDER_ID=".urlencode(urlencode($arOrder["ACCOUNT_NUMBER"])));
			}

			if (strlen($arResult["ERROR_MESSAGE"]) > 0)
				$arResult["CurrentStep"] = 5;


		}
	}
}

/*******************************************************************************/
/***********************************  BODY  ************************************/
/*******************************************************************************/

if($arResult['CurrentStep']==2 || $arResult['CurrentStep']==3)
{
	//for function PrintPropsForm
	$propertyGroupID = 0;
	$propertyUSER_PROPS = "";

	$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
	if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
		$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

	$dbProperties = CSaleOrderProps::GetList(
		array(
			"GROUP_SORT" => "ASC",
			"PROPS_GROUP_ID" => "ASC",
			"SORT" => "ASC",
			"NAME" => "ASC"
		),
		$arFilter,
		false,
		false,
		array("ID", "NAME", "TYPE", "REQUIED", "DEFAULT_VALUE", "IS_LOCATION", "PROPS_GROUP_ID", "SIZE1", "SIZE2", "DESCRIPTION", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "GROUP_NAME", "GROUP_SORT", "SORT", "USER_PROPS", "IS_ZIP")
	);
	while ($arProperties = $dbProperties->GetNext())
	{

		unset($curVal);
		if(isset($arResult["POST"]["ORDER_PROP_".$arProperties["ID"]]))
			$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];

		$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"];
		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID || $propertyUSER_PROPS != $arProperties["USER_PROPS"])
			$arProperties["SHOW_GROUP_NAME"] = "Y";

		$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
		$propertyUSER_PROPS = $arProperties["USER_PROPS"];

		if ($arProperties["REQUIED"]=="Y" || $arProperties["IS_EMAIL"]=="Y" || $arProperties["IS_PROFILE_NAME"]=="Y" || $arProperties["IS_LOCATION"]=="Y" || $arProperties["IS_LOCATION4TAX"]=="Y" || $arProperties["IS_PAYER"]=="Y" || $arProperties["IS_ZIP"]=="Y")
			$arProperties["REQUIED_FORMATED"]="Y";

		if ($arProperties["TYPE"] == "CHECKBOX")
		{
			if ($curVal=="Y" || !isset($curVal) && $arProperties["DEFAULT_VALUE"]=="Y")
				$arProperties["CHECKED"] = "Y";
			$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 30);
		}
		elseif ($arProperties["TYPE"] == "TEXT")
		{
			if (strlen($curVal) <= 0)
			{
				if(strlen($arProperties["DEFAULT_VALUE"])>0 && !isset($curVal))
					$arProperties["VALUE"] = $arProperties["DEFAULT_VALUE"];
				elseif ($arProperties["IS_EMAIL"] == "Y")
					$arProperties["VALUE"] = $USER->GetEmail();
				elseif ($arProperties["IS_PAYER"] == "Y")
				{
					//$arProperties["VALUE"] = $USER->GetFullName();
					$rsUser = CUser::GetByID($USER->GetID());
					$fio = "";
					if ($arUser = $rsUser->Fetch())
					{
						if (strlen($arUser["LAST_NAME"]) > 0)
							$fio .= $arUser["LAST_NAME"];
						if (strlen($arUser["NAME"]) > 0)
							$fio .= " ".$arUser["NAME"];
						if (strlen($arUser["SECOND_NAME"]) > 0 AND strlen($arUser["NAME"]) > 0)
							$fio .= " ".$arUser["SECOND_NAME"];
					}
					$arProperties["VALUE"] = $fio;
				}
			}
			else
				$arProperties["VALUE"] = $curVal;

		}
		elseif ($arProperties["TYPE"] == "SELECT")
		{

			$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
			$arProperties["VARIANTS"] = array();
			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			while ($arVariants = $dbVariants->GetNext()) {

				if ($arVariants["VALUE"] == $curVal || !isset($curVal) && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"])
					$arVariants["SELECTED"] = "Y";
				$arProperties["VARIANTS"][] = $arVariants;
			}
		}

		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$arProperties["FIELD_NAME"] = "ORDER_PROP_".$arProperties["ID"].'[]';
			$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 5);
			$arDefVal = explode(",", $arProperties["DEFAULT_VALUE"]);
			$countDefVal = count($arDefVal);
			for ($i = 0; $i < $countDefVal; $i++)
				$arDefVal[$i] = Trim($arDefVal[$i]);
			$arProperties["VARIANTS"] = array();
			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			while ($arVariants = $dbVariants->GetNext())
			{
				if ((is_array($curVal) && in_array($arVariants["VALUE"], $curVal)) || (!isset($curVal) && in_array($arVariants["VALUE"], $arDefVal)))
					$arVariants["SELECTED"] = "Y";
				$arProperties["VARIANTS"][] = $arVariants;
			}
		}
		elseif ($arProperties["TYPE"] == "TEXTAREA")
		{
			$arProperties["SIZE2"] = ((IntVal($arProperties["SIZE2"]) > 0) ? $arProperties["SIZE2"] : 4);
			$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 40);
			$arProperties["VALUE"] = ((isset($curVal)) ? $curVal : $arProperties["DEFAULT_VALUE"]);
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$locationFound = false;
			$arProperties["SIZE1"] = ((IntVal($arProperties["SIZE1"]) > 0) ? $arProperties["SIZE1"] : 1);
			$arProperties["VARIANTS"] = array();
			$dbVariants = CSaleLocation::GetList(
				array("SORT" => "ASC", "COUNTRY_NAME_LANG" => "ASC", "CITY_NAME_LANG" => "ASC"),
				array("LID" => LANGUAGE_ID),
				false,
				false,
				array("ID", "COUNTRY_NAME", "CITY_NAME", "SORT", "COUNTRY_NAME_LANG", "CITY_NAME_LANG")
			);
			while ($arVariants = $dbVariants->GetNext())
			{
				if (IntVal($arVariants["ID"]) == IntVal($curVal) || !isset($curVal) && IntVal($arVariants["ID"]) == IntVal($arProperties["DEFAULT_VALUE"]))
				{
					$arVariants["SELECTED"] = "Y";
					$locationFound = true;
				}
				$arVariants["NAME"] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
				$arProperties["VARIANTS"][] = $arVariants;
			}

			// this is not a COUNTRY, REGION or CITY, but must appear in $arProperties["VARIANTS"]
			if(CSaleLocation::isLocationProMigrated() && !$locationFound && IntVal($curVal))
			{
				$item = CSaleLocation::GetById($curVal);
				if($item)
				{
					$item['NAME'] = $arVariants["COUNTRY_NAME"].((strlen($arVariants["CITY_NAME"]) > 0) ? " - " : "").$arVariants["CITY_NAME"];
					$item['SELECTED'] = 'Y';
					$arProperties["VARIANTS"][] = $item;
				}
			}
		}
		elseif ($arProperties["TYPE"] == "RADIO")
		{
			$arProperties["VARIANTS"] = array();
			$dbVariants = CSaleOrderPropsVariant::GetList(
				array("SORT" => "ASC"),
				array("ORDER_PROPS_ID" => $arProperties["ID"]),
				false,
				false,
				array("*")
			);
			while ($arVariants = $dbVariants->GetNext())
			{
				if ($arVariants["VALUE"] == $curVal || (strlen($curVal)<=0 && $arVariants["VALUE"] == $arProperties["DEFAULT_VALUE"]))
					$arVariants["CHECKED"]="Y";

				$arProperties["VARIANTS"][] = $arVariants;
			}
		}
		if($arProperties["USER_PROPS"]=="Y")
			$arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"][$arProperties["ID"]] = $arProperties;
		else{
			$arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"][$arProperties["ID"]] = $arProperties;
		}
	}
	if(empty($arResult["PRINT_PROPS_FORM"]["USER_PROPS_Y"]))
	{
		$arResult["USER_PROFILES"] = Array();
		$arResult["USER_PROFILES_TO_FILL_VALUE"] = "N";
		$arResult["USER_PROFILES_TO_FILL"] = "N";

	}
}

if ($arResult["CurrentStep"] == 1)
{
	$arResult["SKIP_FIRST_STEP"] = "N";
	$arResult["SKIP_SECOND_STEP"] = "N";

	$numPersonTypes = 0;
	$curOnePersonType = 0;

	$dbPersonTypesList = CSalePersonType::GetList(
		array("SORT" => "ASC", "NAME" => "ASC"),
		array("LID" => SITE_ID, "ACTIVE" => "Y")
	);
	while ($arPersonTypesList = $dbPersonTypesList->Fetch())
	{
		$numPersonTypes++;
		if ($numPersonTypes >= 2)
			break;

		if ($curOnePersonType <= 0)
			$curOnePersonType = IntVal($arPersonTypesList["ID"]);
	}

	if ($numPersonTypes < 2)
	{
		$arResult["SKIP_FIRST_STEP"] = "Y";
		$arResult["CurrentStep"] = 2;
		$arResult["PERSON_TYPE"] = $curOnePersonType;
	}
}

if ($arResult["CurrentStep"] < 3)
{
	if ($arResult["SKIP_THIRD_STEP"] != "Y" && IntVal($arResult["PERSON_TYPE"]) > 0)
	{
		$arResult["SKIP_THIRD_STEP"] = "N";

		$dbOrderProps = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			array(
				"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
				"IS_LOCATION" => "Y",
				"ACTIVE" => "Y", "UTIL" => "N"
			),
			false,
			false,
			array("ID", "SORT")
		);
		if (!($arOrderProps = $dbOrderProps->Fetch()))
			$arResult["SKIP_THIRD_STEP"] = "Y";
	}

	if($arResult["SKIP_SECOND_STEP"] != "Y" && IntVal($arResult["PERSON_TYPE"]) > 0)
	{
		$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
		if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
			$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

		$dbOrderProps = CSaleOrderProps::GetList(
			array("SORT" => "ASC"),
			$arFilter,
			false,
			false,
			array("ID", "SORT")
		);
		if (!($arOrderProps = $dbOrderProps->Fetch()))
		{
			$arResult["SKIP_SECOND_STEP"] = "Y";
			if($arResult["SKIP_THIRD_STEP"] == "Y")
				$arResult["CurrentStep"] = 4;

		}
	}

	if($arResult["SKIP_SECOND_STEP"] == "Y" && $arResult["BACK"] == "Y")
	{
		$arResult["CurrentStep"] = 1;
	}
	elseif($arResult["SKIP_SECOND_STEP"] == "Y")
	{
		$arResult["CurrentStep"] = 3;
	}
}
if ($arResult["CurrentStep"] == 3)
{
	if (IntVal($arResult["DELIVERY_LOCATION"]) > 0)
	{
		// if your custom handler needs something else, ex. cart content, you may put it here or get it from your handler using API
		$arFilter = array(
			"COMPABILITY" => array(
				"WEIGHT" => $arResult["ORDER_WEIGHT"],
				"PRICE" => $arResult["ORDER_PRICE"],
				"LOCATION_FROM" => COption::GetOptionString('sale', 'location', false, SITE_ID),
				"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
				"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],
			)
		);

		$rsDeliveryServicesList = CSaleDeliveryHandler::GetList(array("SORT" => "ASC"), $arFilter);
		$arDeliveryServicesList = array();
		while ($arDeliveryService = $rsDeliveryServicesList->Fetch())
		{
			$arDeliveryServicesList[] = $arDeliveryService;
		}
		//$numDelivery = count($arDeliveryServicesList);

		$curOneDelivery = false;

		$numDelivery = 0;
		foreach ($arDeliveryServicesList as $key => $arDelivery)
		{
			foreach ($arDelivery['PROFILES'] as $pkey => $arProfile)
			{
				if ($arProfile['ACTIVE'] != 'Y')
				{
					unset($arDeliveryServicesList[$key]['PROFILES'][$pkey]);
				}
			}

			$cnt = count($arDeliveryServicesList[$key]["PROFILES"]);
			if ($cnt <= 0)
				unset($arDeliveryServicesList[$key]);
			else
			{
				$numDelivery += $cnt;
				if($cnt == 1 && empty($curOneDelivery))
				{
					foreach ($arDeliveryServicesList[$key]["PROFILES"] as $pkey => $arProfile)
						$curOneDelivery = array($arDeliveryServicesList[$key]['SID'], $pkey);
				}
			}
		}
		$dbDelivery = CSaleDelivery::GetList(
			array(),
			array(
				"LID" => SITE_ID,
				"+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
				"+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
				"+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
				"+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
				"ACTIVE" => "Y",
				"LOCATION" => $arResult["DELIVERY_LOCATION"],
			)
		);
		while ($arDelivery = $dbDelivery->Fetch())
		{
			$arDeliveryDescription = CSaleDelivery::GetByID($arDelivery["ID"]);
			$arDelivery["DESCRIPTION"] = $arDeliveryDescription["DESCRIPTION"];

			$numDelivery++;
			if ($numDelivery >= 2)
				break;

			if (!is_array($curOneDelivery) || count($curOneDelivery) <= 0 || $curOneDelivery <= 0)
			{
				$curOneDelivery = $arDelivery["ID"];
			}
		}

		if ($numDelivery < 2)
		{
			$arResult["SKIP_THIRD_STEP"] = "Y";
			$arResult["CurrentStep"] = 4;
			$arResult["DELIVERY_ID"] = $curOneDelivery;
		}
	}
	else
	{
		$arResult["SKIP_THIRD_STEP"] = "Y";
		$arResult["CurrentStep"] = 4;
	}
}
if ($arResult["CurrentStep"] == 4)
{
	//if($arResult["PAY_CURRENT_ACCOUNT"] == "N")
	//{
	//if (IntVal($arResult["PAY_SYSTEM_ID"]) <= 0)
	//{
	$numPaySys = 0;
	$curOnePaySys = 0;
	$arFilter = array(
		//"LID" => SITE_ID,
		//"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
		"ACTIVE" => "Y",
		"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
		"PSA_HAVE_PAYMENT" => "Y"
	);
		$deliv = $arResult["DELIVERY_ID"];
	if(is_array($arResult["DELIVERY_ID"]))
		$deliv = $arResult["DELIVERY_ID"][0].":".$arResult["DELIVERY_ID"][1];
	if(!empty($arParams["DELIVERY2PAY_SYSTEM"]))
	{
		foreach($arParams["DELIVERY2PAY_SYSTEM"] as $val)
		{
			if(is_array($val[$deliv]))
			{
				foreach($val[$deliv] as $v)
					$arFilter["ID"][] = $v;
			}
			elseif(IntVal($val[$deliv]) > 0)
				$arFilter["ID"][] = $val[$deliv];
		}
	}

	$dbPaySystem = CSalePaySystem::GetList(
		array("SORT" => "ASC", "PSA_NAME" => "ASC"),
		$arFilter
	);
	while ($arPaySystem = $dbPaySystem->Fetch())
	{
		$numPaySys++;
		if ($numPaySys >= 2)
			break;

		if ($curOnePaySys <= 0)
			$curOnePaySys = $arPaySystem["ID"];
	}


	if ($numPaySys < 2 && $numPaySys > 0)
	{
		if($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
		{
			$dbUserAccount = CSaleUserAccount::GetList(
				array(),
				array(
					"USER_ID" => $USER->GetID(),
					"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
				)
			);
			if ($arUserAccount = $dbUserAccount->GetNext())
			{
				if ($arUserAccount["CURRENT_BUDGET"] <= 0)
				{
					$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
				}
				else
				{
					if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
					{
						if(DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($arResult["ORDER_PRICE"]))
						{
							$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "Y";
						}
						else
							$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
					}
					else
					{
						$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "Y";
					}
				}

			}
			else
				$arParams["ALLOW_PAY_FROM_ACCOUNT"] = "N";
		}


		if($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "N")
		{
			$arResult["SKIP_FORTH_STEP"] = "Y";
			$arResult["CurrentStep"] = 5;
			$arResult["PAY_SYSTEM_ID"] = $curOnePaySys;
		}
	}
	//}
	//else
	//{
	//	$arResult["SKIP_FORTH_STEP"] = "Y";
	//	$arResult["CurrentStep"] = 5;
	//}
	//}
}

//------------------ STEP 1 ----------------------------------------------
if ($arResult["CurrentStep"] == 1)
{
	$arResult["PERSON_TYPE_INFO"] = Array();
	$dbPersonType = CSalePersonType::GetList(
		array("SORT" => "ASC", "NAME" => "ASC"),
		array("LID" => SITE_ID, "ACTIVE" => "Y")
	);
	$bFirst = True;
	while ($arPersonType = $dbPersonType->GetNext())
	{
		if (IntVal($arResult["POST"]["PERSON_TYPE"]) == IntVal($arPersonType["ID"]) || IntVal($arResult["POST"]["PERSON_TYPE"]) <= 0 && $bFirst)
			$arPersonType["CHECKED"] = "Y";
		$arResult["PERSON_TYPE_INFO"][] = $arPersonType;
		$bFirst = False;
	}

	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_1";
		$event3 = "";

		if (is_array($arProductsInBasket))
		{
			foreach($arProductsInBasket as $ar_prod)
			{
				$event3 .= $ar_prod["PRODUCT_ID"].", ";
			}
		}
		$e = $event1."/".$event2."/".$event3;
		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}
}
//------------------ STEP 2 ----------------------------------------------
elseif ($arResult["CurrentStep"] == 2)
{
	//Загружаю компании пользователя
	$arProperties["SIZE1"] = 100;

	$arSelect = Array("ID", "NAME");
	$arFilter = Array(
		"IBLOCK_ID" => USERS_COMPANY_IBLOCK_ID,
		"PROPERTY_USER" => $USER->GetID()
	);

	$arParams["USER_COMPANIES"] = array();
	$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
	while ($ob = $res->GetNext()) {

		$arParams["USER_COMPANIES"][] = $ob["NAME"];
	}

	$arResult["USER_PROFILES"] = Array();
	$bFillProfileFields = False;
	$bFirstProfile = True;

	$dbUserProfiles = CSaleOrderUserProps::GetList(
		array("DATE_UPDATE" => "DESC"),
		array(
			"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
			"USER_ID" => IntVal($USER->GetID())
		)
	);
	if ($arUserProfiles = $dbUserProfiles->GetNext())
	{
		$bFillProfileFields = True;
		do
		{
			if (IntVal($arResult["PROFILE_ID"])==IntVal($arUserProfiles["ID"]) || !isset($arResult["PROFILE_ID"]) && $bFirstProfile)
				$arUserProfiles["CHECKED"] = "Y";
			$bFirstProfile = False;
			$arUserProfiles["USER_PROPS_VALUES"] = Array();
			$dbUserPropsValues = CSaleOrderUserPropsValue::GetList(
				array("SORT" => "ASC"),
				array("USER_PROPS_ID" => $arUserProfiles["ID"]),
				false,
				false,
				array("VALUE", "PROP_TYPE", "VARIANT_NAME", "SORT", "ORDER_PROPS_ID")
			);
			while ($arUserPropsValues = $dbUserPropsValues->GetNext())
			{
				$valueTmp = "";
				if ($arUserPropsValues["PROP_TYPE"] == "SELECT"
					|| $arUserPropsValues["PROP_TYPE"] == "MULTISELECT"
					|| $arUserPropsValues["PROP_TYPE"] == "RADIO")
				{
					$arUserPropsValues["VALUE_FORMATED"] = $arUserPropsValues["VARIANT_NAME"];
				}
				elseif ($arUserPropsValues["PROP_TYPE"] == "LOCATION")
				{
					$arUserPropsValues["VALUE"] = CSaleLocation::getLocationIDByCode($arUserPropsValues["VALUE"]);

					if ($arLocation = CSaleLocation::GetByID($arUserPropsValues["VALUE"], LANGUAGE_ID))
					{
						$locationName = '';
						if(CSaleLocation::isLocationProMigrated())
						{
							$locationName = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationStringById($arLocation['ID']);
						}
						else
						{
							$locationName .= ((strlen($arLocation["COUNTRY_NAME"])<=0) ? "" : $arLocation["COUNTRY_NAME"]);
							if (strlen($arLocation["COUNTRY_NAME"])>0 && strlen($arLocation["REGION_NAME"])>0)
								$locationName .= " - ".$arLocation["REGION_NAME"];
							elseif (strlen($arLocation["REGION_NAME"])>0)
								$locationName .= $arLocation["REGION_NAME"];
							if (strlen($arLocation["COUNTRY_NAME"])>0 || strlen($arLocation["REGION_NAME"])>0)
								$locationName .= " - ".$arLocation["CITY_NAME"];
							elseif (strlen($arLocation["CITY_NAME"])>0)
								$locationName .= $arLocation["CITY_NAME"];
						}

						$arUserPropsValues["VALUE_FORMATED"] = $locationName;
					}
				}
				else
					$arUserPropsValues["VALUE_FORMATED"] = $arUserPropsValues["VALUE"];
				$arUserProfiles["USER_PROPS_VALUES"][] = $arUserPropsValues;
			}
			$arResult["USER_PROFILES"][] = $arUserProfiles;
		}
		while ($arUserProfiles = $dbUserProfiles->GetNext());

		if (isset($arResult["PROFILE_ID"]) && IntVal($arResult["PROFILE_ID"]) > 0 && $bFirstProfile)
			$arResult["USER_PROFILES_0"] = "Y";

	}

	if ($bFillProfileFields)
	{
		$arResult["USER_PROFILES_TO_FILL"] = "Y";
		if(isset($arResult["PROFILE_ID"]) && IntVal($arResult["PROFILE_ID"]) > 0 && $bFirstProfile)
			$arResult["USER_PROFILES_TO_FILL_VALUE"] = "Y";
	}


	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_2";
		$event3 = "";

		foreach($arProductsInBasket as $ar_prod)
		{
			$event3 .= $ar_prod["PRODUCT_ID"].", ";
		}
		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}
}
//------------------ STEP 3 ----------------------------------------------
elseif ($arResult["CurrentStep"] == 3)
{
	$arResult["DELIVERY"] = Array();

	$deliv = $arResult["DELIVERY_ID"];
	if(is_array($arResult["DELIVERY_ID"]))
		$deliv = $arResult["DELIVERY_ID"][0].":".$arResult["DELIVERY_ID"][1];

	$arOrderPaySystem=array("SORT"=>"ASC");
	$arFilterPaySystem=array("ACTIVE"=>"Y","PERSON_TYPE_ID"=>$arResult['PERSON_TYPE']);
	$arSelectPaySystem=array();
	$dbPaySystem = CSalePaySystem::GetList($arOrderPaySystem,$arFilterPaySystem,false,false,$arSelectPaySystem);
	if($dbPaySystem->SelectedRowsCount())
	{
		while($arPaySystem=$dbPaySystem->GetNext())
		{
			$arResult['PAY_SYSTEM'][]=$arPaySystem['ID'];
		}
	}
	
	$dbDelivery = CSaleDelivery::GetList(
		array("SORT"=>"ASC", "NAME"=>"ASC"),
		array(
			"LID" => SITE_ID,
			"+<=WEIGHT_FROM" => $arResult["ORDER_WEIGHT"],
			"+>=WEIGHT_TO" => $arResult["ORDER_WEIGHT"],
			"+<=ORDER_PRICE_FROM" => $arResult["ORDER_PRICE"],
			"+>=ORDER_PRICE_TO" => $arResult["ORDER_PRICE"],
			"ACTIVE" => "Y",
			"LOCATION" => $arResult["DELIVERY_LOCATION"]
		)
	);

	$bFirst = True;
	while ($arDelivery = $dbDelivery->GetNext())
	{
		$arDelivery["FIELD_NAME"] = "DELIVERY_ID";
		if (IntVal($arResult["DELIVERY_ID"]) == IntVal($arDelivery["ID"])
			|| IntVal($arResult["DELIVERY_ID"]) <= 0 && $bFirst)
			$arDelivery["CHECKED"] = "Y";
		if (IntVal($arDelivery["PERIOD_FROM"]) > 0 || IntVal($arDelivery["PERIOD_TO"]) > 0)
		{
			$arDelivery["PERIOD_TEXT"] = GetMessage("SALE_DELIV_PERIOD");
			if (IntVal($arDelivery["PERIOD_FROM"]) > 0)
				$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_FROM")." ".IntVal($arDelivery["PERIOD_FROM"]);
			if (IntVal($arDelivery["PERIOD_TO"]) > 0)
				$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_TO")." ".IntVal($arDelivery["PERIOD_TO"]);
			if ($arDelivery["PERIOD_TYPE"] == "H")
				$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_HOUR")." ";
			elseif ($arDelivery["PERIOD_TYPE"]=="M")
				$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_MONTH")." ";
			else
				$arDelivery["PERIOD_TEXT"] .= " ".GetMessage("SALE_PER_DAY")." ";
		}
		$arDelivery["PRICE_FORMATED"] = SaleFormatCurrency($arDelivery["PRICE"], $arDelivery["CURRENCY"]);
		$arResult["DELIVERY"][] = $arDelivery;
		$arResult['DELIVERY_ID_BY_LOCATION'][]=$arDelivery['ID'];
		$bFirst = false;
	}
	if (is_array($arDeliveryServicesList))
	{
		$bFirst = true;
		foreach ($arDeliveryServicesList as $arDeliveryInfo)
		{
			$delivery_id = $arDeliveryInfo["SID"];

			if (!is_array($arDeliveryInfo) || !is_array($arDeliveryInfo["PROFILES"])) continue;

			foreach ($arDeliveryInfo["PROFILES"] as $profile_id => $arDeliveryProfile)
			{
				$arProfile = array(
					"SID" => $profile_id,
					"TITLE" => $arDeliveryProfile["TITLE"],
					"DESCRIPTION" => $arDeliveryProfile["DESCRIPTION"],
					//"CHECKED" => $bFirst ? "Y" : "N",
					"FIELD_NAME" => "DELIVERY_ID",
				);

				if ($arResult['DELIVERY_ID'])
					if(strpos($deliv, ":") !== false &&
						$deliv == $delivery_id.":".$profile_id
						|| empty($arResult["DELIVERY_ID"]) && $bFirst
					)
						$arProfile["CHECKED"] = "Y";

				if (!is_array($arResult["DELIVERY"][$delivery_id]))
				{
					$arResult["DELIVERY"][$delivery_id] = array(
						"SID" => $delivery_id,
						"TITLE" => $arDeliveryInfo["NAME"],
						"DESCRIPTION" => $arDeliveryInfo["DESCRIPTION"],
						"PROFILES" => array(),
					);
				}

				$arResult["DELIVERY"][$delivery_id]["PROFILES"][$profile_id] = $arProfile;

				$bFirst = false;
			}
		}
	}

	foreach($arResult['DELIVERY'] as $arDelivery)
	{
		if(intval($arDelivery['ID']))
		{
			$dbRes = CSaleDelivery::GetDelivery2PaySystem(array("DELIVERY_ID" => $arDelivery['ID']));
			while($arRes=$dbRes->GetNext())
			{
				if(in_array($arRes['PAYSYSTEM_ID'],$arResult['PAY_SYSTEM']))
				{
					$arResult['DELIVERY_ID_BY_PAY_SYSTEM'][] = $arRes['DELIVERY_ID'];
				}
			}
		}
	}
	$arResult['DELIVERY_ID_BY_PAY_SYSTEM'] = array_unique($arResult['DELIVERY_ID_BY_PAY_SYSTEM']);
	if(!empty($arResult['DELIVERY_ID_BY_LOCATION'])&&!empty($arResult['DELIVERY_ID_BY_PAY_SYSTEM']))
	{
		$arResult['DELIVERY_ID']= array_intersect($arResult['DELIVERY_ID_BY_LOCATION'],$arResult['DELIVERY_ID_BY_PAY_SYSTEM']);
	}

	// проверяем, доступна ли доставка до двери в выбранном нами городе
	$arResult['DELIVERY_TO_DOOR_AVAILABLE']=true;
	if(in_array(TO_DOOR_RETAIL_FREE_ID,$arResult['DELIVERY_ID'])|| in_array(TO_DOOR_RETAIL_PAID_ID,$arResult['DELIVERY_ID'])
		||in_array(TO_DOOR_WHOLESALE_FREE_ID,$arResult['DELIVERY_ID'])||in_array(TO_DOOR_WHOLESALE_PAID_ID,$arResult['DELIVERY_ID']))
	{
		$apiId = "5123092bc869572a129874827269e5ed"; // указывается в настройках службы доставки
		$dbCity = CSaleLocation::GetByID($APPLICATION->get_cookie("CITY_ID"), 'ru');
		$cityList = CDeliveryShoplogistic::getCityList($apiId);
		if(!isset($cityList['INDEX_NAME'][$dbCity['CITY_NAME']]))
		{
			$arResult['DELIVERY_TO_DOOR_AVAILABLE']=false;
		}
	}
	if(!empty($arResult['DELIVERY_ID']))
	{
		foreach($arResult['DELIVERY'] as $key=>$arDelivery)
		{
			if(!in_array($arDelivery['ID'],$arResult['DELIVERY_ID'])&&!in_array($arDelivery['SID'],	$arResult['DELIVERY_ID']))
			{
				unset($arResult['DELIVERY'][$key]);
			}

			// если доставка недоступна - удалим соответствующую службу доставки
			if($arResult['DELIVERY_TO_DOOR_AVAILABLE']===false)
			{
				if($arDelivery['ID']==TO_DOOR_WHOLESALE_PAID_ID || $arDelivery['ID']==TO_DOOR_WHOLESALE_FREE_ID ||
				$arDelivery['ID']==TO_DOOR_RETAIL_PAID_ID || $arDelivery['ID']==TO_DOOR_RETAIL_FREE_ID)
				{
					unset($arResult['DELIVERY'][$key]);
				}
			}
		}
	}
	
	$arResult['PICKUP_LIST'] = CDeliveryShoplogistic::getPickupListByBitrixLocation($APPLICATION->get_cookie("CITY_ID"));
	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_3";
		$event3 = "";

		foreach($arProductsInBasket as $ar_prod)
		{
			$event3 .= $ar_prod["PRODUCT_ID"].", ";
		}
		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}
}
//------------------ STEP 4 ----------------------------------------------
elseif ($arResult["CurrentStep"] == 4)
{
	if ($arParams["ALLOW_PAY_FROM_ACCOUNT"] == "Y")
	{
		$dbUserAccount = CSaleUserAccount::GetList(
			array(),
			array(
				"USER_ID" => $USER->GetID(),
				"CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
			)
		);
		if ($arUserAccount = $dbUserAccount->GetNext())
		{
			if ($arUserAccount["CURRENT_BUDGET"] > 0)
			{

				if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y")
				{
					if(DoubleVal($arUserAccount["CURRENT_BUDGET"]) >= DoubleVal($arResult["ORDER_PRICE"]))
					{
						$arResult["PAY_FROM_ACCOUNT"] = "Y";
						$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
						$arResult["USER_ACCOUNT"] = $arUserAccount;
					}
					else
						$arResult["PAY_FROM_ACCOUNT"] = "N";
				}
				else
				{
					$arResult["PAY_FROM_ACCOUNT"] = "Y";
					$arResult["CURRENT_BUDGET_FORMATED"] = SaleFormatCurrency($arUserAccount["CURRENT_BUDGET"], $arResult["BASE_LANG_CURRENCY"]);
					$arResult["USER_ACCOUNT"] = $arUserAccount;
				}
			}
		}
	}
	$arResult["PAY_SYSTEM"] = Array();
	$arFilter = array(
		"ACTIVE" => "Y",
		"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"],
		"PSA_HAVE_PAYMENT" => "Y"
	);
	$deliv = $arResult["DELIVERY_ID"];
	if(is_array($arResult["DELIVERY_ID"]))
		$deliv = $arResult["DELIVERY_ID"][0].":".$arResult["DELIVERY_ID"][1];
	if(!empty($arParams["DELIVERY2PAY_SYSTEM"]))
	{
		foreach($arParams["DELIVERY2PAY_SYSTEM"] as $val)
		{
			if(is_array($val[$deliv]))
			{
				foreach($val[$deliv] as $v)
					$arFilter["ID"][] = $v;
			}
			elseif(IntVal($val[$deliv]) > 0)
				$arFilter["ID"][] = $val[$deliv];
		}
	}

	//select delivery to pay
	$bShowDefault = False;
	$arD2P = array();
	$dbRes = CSaleDelivery::GetDelivery2PaySystem(array("DELIVERY_ID" => $deliv));
	while ($arRes = $dbRes->Fetch())
	{
		$arD2P[] = $arRes["PAYSYSTEM_ID"];
		$bShowDefault = True;
	}


	$dbPaySystem = CSalePaySystem::GetList(
		array("SORT" => "ASC", "PSA_NAME" => "ASC"),
		$arFilter
	);
	$bFirst = True;
	while ($arPaySystem = $dbPaySystem->Fetch())
	{
		if (!$bShowDefault || in_array($arPaySystem["ID"], $arD2P))
		{
			if ($arPaySystem["PSA_LOGOTIP"] > 0)
				$arPaySystem["PSA_LOGOTIP"] = CFile::GetFileArray($arPaySystem["PSA_LOGOTIP"]);

			if (IntVal($arResult["PAY_SYSTEM_ID"]) == IntVal($arPaySystem["ID"]) || IntVal($arResult["PAY_SYSTEM_ID"]) <= 0 && $bFirst)
				$arPaySystem["CHECKED"] = "Y";
			$arPaySystem["PSA_NAME"] = htmlspecialcharsEx($arPaySystem["PSA_NAME"]);
			$arResult["PAY_SYSTEM"][] = $arPaySystem;
			$bFirst = false;
		}
	}

	$bHaveTaxExempts = False;
	if (is_array($arResult["TaxExempt"]) && count($arResult["TaxExempt"])>0)
	{
		$dbTaxRateList = CSaleTaxRate::GetList(
			array("APPLY_ORDER" => "ASC"),
			array(
				"LID" => SITE_ID,
				"PERSON_TYPE_ID" => $PERSON_TYPE,
				"IS_IN_PRICE" => "N",
				"ACTIVE" => "Y",
				"LOCATION" => IntVal($TAX_LOCATION)
			)
		);
		while ($arTaxRateList = $dbTaxRateList->GetNext())
		{
			if (in_array(IntVal($arTaxRateList["TAX_ID"]), $arResult["TaxExempt"]))
			{
				$arResult["HaveTaxExempts"] = "Y";
				break;
			}
		}
	}

	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_4";
		$event3 = "";

		foreach($arProductsInBasket as $ar_prod)
		{
			$event3 .= $ar_prod["PRODUCT_ID"].", ";
		}
		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}
	$arResult["ORDER_PROPS_PRINT"] = Array();
	$propertyGroupID = -1;

	$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
	if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
		$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

	$dbProperties = CSaleOrderProps::GetList(
		array(
			"GROUP_SORT" => "ASC",
			"PROPS_GROUP_ID" => "ASC",
			"SORT" => "ASC",
			"NAME" => "ASC"
		),
		$arFilter,
		false,
		false,
		array("ID", "NAME", "TYPE", "PROPS_GROUP_ID", "GROUP_NAME", "GROUP_SORT", "SORT", "CODE")
	);
	while ($arProperties = $dbProperties->GetNext())
	{
		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
		{
			$arProperties["SHOW_GROUP_NAME"] = "Y";
			$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
		}
		$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];
		if ($arProperties["TYPE"] == "CHECKBOX")
		{
			if ($curVal == "Y")
				$arProperties["VALUE_FORMATED"] = GetMessage("SALE_YES");
			else
				$arProperties["VALUE_FORMATED"] = GetMessage("SALE_NO");
		}
		elseif ($arProperties["TYPE"] == "TEXT" || $arProperties["TYPE"] == "TEXTAREA")
		{
			$arProperties["VALUE_FORMATED"] = $curVal;
		}
		elseif ($arProperties["TYPE"] == "SELECT" || $arProperties["TYPE"] == "RADIO")
		{
			$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal);
			$arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["NAME"]);
		}
		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$countCurVal = count($curVal);
			for ($i = 0; $i < $countCurVal; $i++)
			{
				$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal[$i]);
				if ($i > 0)
					$arProperties["VALUE_FORMATED"] .= ", ";
				$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["NAME"]);
			}
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$arVal = CSaleLocation::GetByID($curVal, LANGUAGE_ID);
			/*
            $arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["COUNTRY_NAME"]);
            if (strlen($arVal["COUNTRY_NAME"]) > 0 && strlen($arVal["CITY_NAME"]) > 0)
                $arProperties["VALUE_FORMATED"] .= " - ";
            $arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["CITY_NAME"]);
            */

			$locationName = "";

			if(CSaleLocation::isLocationProMigrated())
			{
				if(intval($arVal['ID']))
					$locationName = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationStringById($arVal['ID']);
			}
			else
			{
				$locationName .= ((strlen($arVal["COUNTRY_NAME"])<=0) ? "" : $arVal["COUNTRY_NAME"]);

				if (strlen($arVal["COUNTRY_NAME"])>0 && strlen($arVal["REGION_NAME"])>0)
					$locationName .= " - ".$arVal["REGION_NAME"];
				elseif (strlen($arVal["REGION_NAME"])>0)
					$locationName .= $arVal["REGION_NAME"];

				if (strlen($arVal["COUNTRY_NAME"])>0 || strlen($arVal["REGION_NAME"])>0)
					$locationName .= " - ".$arVal["CITY_NAME"];
				elseif (strlen($arVal["CITY_NAME"])>0)
					$locationName .= $arVal["CITY_NAME"];
			}

			$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($locationName);
		}
		$arResult["ORDER_PROPS_PRINT"][] = $arProperties;
	}

	if (is_array($arResult["DELIVERY_ID"]))
	{
		$obDeliveryHandler = CSaleDeliveryHandler::GetBySID($arResult["DELIVERY_ID"][0]);
		$arResult["DELIVERY"] = $obDeliveryHandler->Fetch();

		$arResult["DELIVERY_PROFILE"] = $arResult["DELIVERY_ID"][1];

		$arOrderTmpDel = array(
			"PRICE" => $arResult["ORDER_PRICE"],
			"WEIGHT" => $arResult["ORDER_WEIGHT"],
			"LOCATION_FROM" => COption::GetOptionString('sale', 'location'),
			"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
			"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],

		);

		$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

		if ($arDeliveryPrice["RESULT"] == "ERROR")
			$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
		else
			$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);

	}
	elseif ((IntVal($arResult["DELIVERY_ID"]) > 0) && ($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
	{
		$arDeliv["NAME"] = htmlspecialcharsEx($arDeliv["NAME"]);
		$arResult["DELIVERY"] = $arDeliv;
		$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
	}
	elseif (IntVal($DELIVERY_ID)>0)
	{
		$arResult["DELIVERY"] = "ERROR";
	}

	if ((IntVal($arResult["PAY_SYSTEM_ID"]) > 0) && ($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
	{
		$arResult["PAY_SYSTEM"] = $arPaySys;
		$arResult["PAY_SYSTEM"]["PSA_NAME"] = htmlspecialcharsEx($arResult["PAY_SYSTEM"]["PSA_NAME"]);
		$arResult["PAY_SYSTEM"]["~PSA_NAME"] = $arResult["PAY_SYSTEM"]["PSA_NAME"];
	}
	elseif (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
	{
		$arResult["PAY_SYSTEM"] = "ERROR";
	}

	$arResult["BASKET_ITEMS"] = Array();
	$arResult["ORDER_WEIGHT"] = 0;

	CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
	$dbBasketItems = CSaleBasket::GetList(
		array("ID" => "ASC"),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL"
		)
	);
	while ($arBasketItems = $dbBasketItems->Fetch())
	{
		if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
		{
			$arBasketItems['NAME'] = htmlspecialcharsEx($arBasketItems['NAME']);
			$arBasketItems['NOTES'] = htmlspecialcharsEx($arBasketItems['NOTES']);
			$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
			$arBasketItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arBasketItems["WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];

			$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
			if(DoubleVal($arBasketItems["DISCOUNT_PRICE"]) > 0)
			{
				if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
					$arBasketItems["VAT_VALUE"] = DoubleVal(($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]);

				$arBasketItems["DISCOUNT_PRICE_PERCENT"] = $arBasketItems["DISCOUNT_PRICE"]*100 / ($arBasketItems["DISCOUNT_PRICE"] + $arBasketItems["PRICE"]);
				$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arBasketItems["DISCOUNT_PRICE_PERCENT"], 0)."%";
			}

			$arBasketItems["PROPS"] = Array();
			$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasketItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
			while($arProp = $dbProp -> GetNext())
				$arBasketItems["PROPS"][] = $arProp;


			$arResult["BASKET_ITEMS"][] = $arBasketItems;
		}
	}

	$arResult["ORDER_WEIGHT_FORMATED"] = roundEx(DoubleVal($arResult["ORDER_WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
	$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$DISCOUNT_PRICE_ALL += $arResult["DISCOUNT_PRICE"];

	if (DoubleVal($arResult["DISCOUNT_PERCENT"])>0)
		$arResult["DISCOUNT_PERCENT_FORMATED"] = DoubleVal($arResult["DISCOUNT_PERCENT"])."%";
	if (is_array($arResult["arTaxList"]) && count($arResult["arTaxList"])>0)
	{
		foreach ($arResult["arTaxList"] as $key => $val)
		{
			if ($val["IS_IN_PRICE"]=="Y")
			{
				$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".(($val["IS_PERCENT"]=="Y")?"".DoubleVal($val["VALUE"])."%, ":" ").GetMessage("SALE_TAX_INPRICE").")";
			}
			elseif ($val["IS_PERCENT"]=="Y")
			{
				$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".DoubleVal($val["VALUE"])."%)";
			}
			$arResult["arTaxList"][$key]["VALUE_MONEY_FORMATED"] = SaleFormatCurrency($val["VALUE_MONEY"], $arResult["BASE_LANG_CURRENCY"]);
		}
	}

	if(IntVal($arResult["DELIVERY_PRICE"])>0)
		$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$orderTotalSum = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
	$arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($orderTotalSum, $arResult["BASE_LANG_CURRENCY"]);
	if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y")
	{
		$dbUserAccount = CSaleUserAccount::GetList(
			array(),
			array(
				"USER_ID" => $USER->GetID(),
				"CURRENCY" => $arResult["BASE_LANG_CURRENCY"]
			)
		);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			if ($arUserAccount["CURRENT_BUDGET"] > 0)
			{
				$arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arUserAccount["CURRENT_BUDGET"]),	$arResult["BASE_LANG_CURRENCY"]);
			}
			if($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum)
			{
				$arResult["PAYED_FROM_ACCOUNT"] = "Y";
			}
		}
	}

	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_5";
		$event3 = "";

		foreach($arProductsInBasket as $ar_prod)
		{
			$event3 .= $ar_prod["PRODUCT_ID"].", ";
		}
		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}
}
//------------------ STEP 5 ----------------------------------------------
elseif ($arResult["CurrentStep"] == 5)
{
	$arResult["ORDER_PROPS_PRINT"] = Array();
	$propertyGroupID = -1;

	$arFilter = array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N");
	if(!empty($arParams["PROP_".$arResult["PERSON_TYPE"]]))
		$arFilter["!ID"] = $arParams["PROP_".$arResult["PERSON_TYPE"]];

	$dbProperties = CSaleOrderProps::GetList(
		array(
			"GROUP_SORT" => "ASC",
			"PROPS_GROUP_ID" => "ASC",
			"SORT" => "ASC",
			"NAME" => "ASC"
		),
		$arFilter,
		false,
		false,
		array("ID", "NAME", "TYPE", "PROPS_GROUP_ID", "GROUP_NAME", "GROUP_SORT", "SORT")
	);
	while ($arProperties = $dbProperties->GetNext())
	{
		if (IntVal($arProperties["PROPS_GROUP_ID"]) != $propertyGroupID)
		{
			$arProperties["SHOW_GROUP_NAME"] = "Y";
			$propertyGroupID = $arProperties["PROPS_GROUP_ID"];
		}
		$curVal = $arResult["POST"]["ORDER_PROP_".$arProperties["ID"]];
		if ($arProperties["TYPE"] == "CHECKBOX")
		{
			if ($curVal == "Y")
				$arProperties["VALUE_FORMATED"] = GetMessage("SALE_YES");
			else
				$arProperties["VALUE_FORMATED"] = GetMessage("SALE_NO");
		}
		elseif ($arProperties["TYPE"] == "TEXT" || $arProperties["TYPE"] == "TEXTAREA")
		{
			$arProperties["VALUE_FORMATED"] = $curVal;
		}
		elseif ($arProperties["TYPE"] == "SELECT" || $arProperties["TYPE"] == "RADIO")
		{
			$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal);
			$arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["NAME"]);
		}
		elseif ($arProperties["TYPE"] == "MULTISELECT")
		{
			$countCurVal = count($curVal);
			for ($i = 0; $i < $countCurVal; $i++)
			{
				$arVal = CSaleOrderPropsVariant::GetByValue($arProperties["ID"], $curVal[$i]);
				if ($i > 0)
					$arProperties["VALUE_FORMATED"] .= ", ";
				$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["NAME"]);
			}
		}
		elseif ($arProperties["TYPE"] == "LOCATION")
		{
			$arVal = CSaleLocation::GetByID($curVal, LANGUAGE_ID);
			/*
            $arProperties["VALUE_FORMATED"] = htmlspecialcharsEx($arVal["COUNTRY_NAME"]);
            if (strlen($arVal["COUNTRY_NAME"]) > 0 && strlen($arVal["CITY_NAME"]) > 0)
                $arProperties["VALUE_FORMATED"] .= " - ";
            $arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($arVal["CITY_NAME"]);
            */

			$locationName = "";

			if(CSaleLocation::isLocationProMigrated())
			{
				if(intval($arVal['ID']))
					$locationName = \Bitrix\Sale\Location\Admin\LocationHelper::getLocationStringById($arVal['ID']);
			}
			else
			{
				$locationName .= ((strlen($arVal["COUNTRY_NAME"])<=0) ? "" : $arVal["COUNTRY_NAME"]);

				if (strlen($arVal["COUNTRY_NAME"])>0 && strlen($arVal["REGION_NAME"])>0)
					$locationName .= " - ".$arVal["REGION_NAME"];
				elseif (strlen($arVal["REGION_NAME"])>0)
					$locationName .= $arVal["REGION_NAME"];

				if (strlen($arVal["COUNTRY_NAME"])>0 || strlen($arVal["REGION_NAME"])>0)
					$locationName .= " - ".$arVal["CITY_NAME"];
				elseif (strlen($arVal["CITY_NAME"])>0)
					$locationName .= $arVal["CITY_NAME"];
			}

			$arProperties["VALUE_FORMATED"] .= htmlspecialcharsEx($locationName);
		}
		$arResult["ORDER_PROPS_PRINT"][] = $arProperties;
	}

	if (is_array($arResult["DELIVERY_ID"]))
	{
		$obDeliveryHandler = CSaleDeliveryHandler::GetBySID($arResult["DELIVERY_ID"][0]);
		$arResult["DELIVERY"] = $obDeliveryHandler->Fetch();

		$arResult["DELIVERY_PROFILE"] = $arResult["DELIVERY_ID"][1];

		$arOrderTmpDel = array(
			"PRICE" => $arResult["ORDER_PRICE"],
			"WEIGHT" => $arResult["ORDER_WEIGHT"],
			"LOCATION_FROM" => COption::GetOptionString('sale', 'location'),
			"LOCATION_TO" => $arResult["DELIVERY_LOCATION"],
			"LOCATION_ZIP" => $arResult["DELIVERY_LOCATION_ZIP"],

		);

		$arDeliveryPrice = CSaleDeliveryHandler::CalculateFull($arResult["DELIVERY_ID"][0], $arResult["DELIVERY_ID"][1], $arOrderTmpDel, $arResult["BASE_LANG_CURRENCY"]);

		if ($arDeliveryPrice["RESULT"] == "ERROR")
			$arResult["ERROR_MESSAGE"] = $arDeliveryPrice["TEXT"];
		else
			$arResult["DELIVERY_PRICE"] = roundEx($arDeliveryPrice["VALUE"], SALE_VALUE_PRECISION);

	}
	elseif ((IntVal($arResult["DELIVERY_ID"]) > 0) && ($arDeliv = CSaleDelivery::GetByID($arResult["DELIVERY_ID"])))
	{
		$arDeliv["NAME"] = htmlspecialcharsEx($arDeliv["NAME"]);
		$arResult["DELIVERY"] = $arDeliv;
		$arResult["DELIVERY_PRICE"] = roundEx(CCurrencyRates::ConvertCurrency($arDeliv["PRICE"], $arDeliv["CURRENCY"], $arResult["BASE_LANG_CURRENCY"]), SALE_VALUE_PRECISION);
	}
	elseif (IntVal($DELIVERY_ID)>0)
	{
		$arResult["DELIVERY"] = "ERROR";
	}

	if ((IntVal($arResult["PAY_SYSTEM_ID"]) > 0) && ($arPaySys = CSalePaySystem::GetByID($arResult["PAY_SYSTEM_ID"], $arResult["PERSON_TYPE"])))
	{
		$arResult["PAY_SYSTEM"] = $arPaySys;
		$arResult["PAY_SYSTEM"]["PSA_NAME"] = htmlspecialcharsEx($arResult["PAY_SYSTEM"]["PSA_NAME"]);
		$arResult["PAY_SYSTEM"]["~PSA_NAME"] = $arResult["PAY_SYSTEM"]["PSA_NAME"];
	}
	elseif (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
	{
		$arResult["PAY_SYSTEM"] = "ERROR";
	}

	$arResult["BASKET_ITEMS"] = Array();
	$arResult["ORDER_WEIGHT"] = 0;

	CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID(), SITE_ID);
	$dbBasketItems = CSaleBasket::GetList(
		array("ID" => "ASC"),
		array(
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL"
		)
	);
	while ($arBasketItems = $dbBasketItems->Fetch())
	{
		if ($arBasketItems["DELAY"] == "N" && $arBasketItems["CAN_BUY"] == "Y")
		{
			$arBasketItems['NAME'] = htmlspecialcharsEx($arBasketItems['NAME']);
			$arBasketItems['NOTES'] = htmlspecialcharsEx($arBasketItems['NOTES']);
			$arResult["ORDER_WEIGHT"] += $arBasketItems["WEIGHT"] * $arBasketItems["QUANTITY"];
			$arBasketItems["WEIGHT_FORMATED"] = roundEx(DoubleVal($arBasketItems["WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];

			$arBasketItems["PRICE_FORMATED"] = SaleFormatCurrency($arBasketItems["PRICE"], $arBasketItems["CURRENCY"]);
			if(DoubleVal($arBasketItems["DISCOUNT_PRICE"]) > 0)
			{
				if(DoubleVal($arBasketItems["VAT_RATE"]) > 0)
					$arBasketItems["VAT_VALUE"] = DoubleVal(($arBasketItems["PRICE"] / ($arBasketItems["VAT_RATE"] +1)) * $arBasketItems["VAT_RATE"]);

				$arBasketItems["DISCOUNT_PRICE_PERCENT"] = $arBasketItems["DISCOUNT_PRICE"]*100 / ($arBasketItems["DISCOUNT_PRICE"] + $arBasketItems["PRICE"]);
				$arBasketItems["DISCOUNT_PRICE_PERCENT_FORMATED"] = roundEx($arBasketItems["DISCOUNT_PRICE_PERCENT"], 0)."%";
			}

			$arBasketItems["PROPS"] = Array();
			$dbProp = CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasketItems["ID"], "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID")));
			while($arProp = $dbProp -> GetNext())
				$arBasketItems["PROPS"][] = $arProp;


			$arResult["BASKET_ITEMS"][] = $arBasketItems;
		}
	}

	$arResult["ORDER_WEIGHT_FORMATED"] = roundEx(DoubleVal($arResult["ORDER_WEIGHT"]/$arResult["WEIGHT_KOEF"]), SALE_WEIGHT_PRECISION)." ".$arResult["WEIGHT_UNIT"];
	$arResult["ORDER_PRICE_FORMATED"] = SaleFormatCurrency($arResult["ORDER_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$arResult["DISCOUNT_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DISCOUNT_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$DISCOUNT_PRICE_ALL += $arResult["DISCOUNT_PRICE"];

	if (DoubleVal($arResult["DISCOUNT_PERCENT"])>0)
		$arResult["DISCOUNT_PERCENT_FORMATED"] = DoubleVal($arResult["DISCOUNT_PERCENT"])."%";
	if (is_array($arResult["arTaxList"]) && count($arResult["arTaxList"])>0)
	{
		foreach ($arResult["arTaxList"] as $key => $val)
		{
			if ($val["IS_IN_PRICE"]=="Y")
			{
				$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".(($val["IS_PERCENT"]=="Y")?"".DoubleVal($val["VALUE"])."%, ":" ").GetMessage("SALE_TAX_INPRICE").")";
			}
			elseif ($val["IS_PERCENT"]=="Y")
			{
				$arResult["arTaxList"][$key]["VALUE_FORMATED"] = " (".DoubleVal($val["VALUE"])."%)";
			}
			$arResult["arTaxList"][$key]["VALUE_MONEY_FORMATED"] = SaleFormatCurrency($val["VALUE_MONEY"], $arResult["BASE_LANG_CURRENCY"]);
		}
	}

	if(IntVal($arResult["DELIVERY_PRICE"])>0)
		$arResult["DELIVERY_PRICE_FORMATED"] = SaleFormatCurrency($arResult["DELIVERY_PRICE"], $arResult["BASE_LANG_CURRENCY"]);
	$orderTotalSum = $arResult["ORDER_PRICE"] + $arResult["DELIVERY_PRICE"] + $arResult["TAX_PRICE"] - $arResult["DISCOUNT_PRICE"];
	$arResult["ORDER_TOTAL_PRICE_FORMATED"] = SaleFormatCurrency($orderTotalSum, $arResult["BASE_LANG_CURRENCY"]);
	if ($arResult["PAY_CURRENT_ACCOUNT"] == "Y")
	{
		$dbUserAccount = CSaleUserAccount::GetList(
			array(),
			array(
				"USER_ID" => $USER->GetID(),
				"CURRENCY" => $arResult["BASE_LANG_CURRENCY"]
			)
		);
		if ($arUserAccount = $dbUserAccount->Fetch())
		{
			if ($arUserAccount["CURRENT_BUDGET"] > 0)
			{
				$arResult["PAYED_FROM_ACCOUNT_FORMATED"] = SaleFormatCurrency((($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum) ? $orderTotalSum : $arUserAccount["CURRENT_BUDGET"]),	$arResult["BASE_LANG_CURRENCY"]);
			}
			if($arUserAccount["CURRENT_BUDGET"] >= $orderTotalSum)
			{
				$arResult["PAYED_FROM_ACCOUNT"] = "Y";
			}
		}
	}

	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "Step4_5";
		$event3 = "";

		foreach($arProductsInBasket as $ar_prod)
		{
			$event3 .= $ar_prod["PRODUCT_ID"].", ";
		}
		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}

	$arOrder = false;
	if(intval($arResult['ORDER_ID']))
	{
		if ($bUseAccountNumber) // supporting ACCOUNT_NUMBER or ID in the URL
		{
			$dbOrder = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
					"LID" => SITE_ID,
					"USER_ID" => IntVal($USER->GetID()),
					"ACCOUNT_NUMBER" => $arResult['ORDER_ID']
				)
			);

			if ($arOrder = $dbOrder->GetNext())
			{
				$arResult["ORDER_ID"] = $arOrder["ID"];
			}
		}
		if (!$arOrder)
		{
			$dbOrder = CSaleOrder::GetList(
				array("DATE_UPDATE" => "DESC"),
				array(
					"LID" => SITE_ID,
					"ID" => $arResult['ORDER_ID']
				)
			);
			$arOrder = $dbOrder->GetNext();
		}
	}
	if (IntVal($arResult["PAY_SYSTEM_ID"]) > 0)
	{
		$dbPaySysAction = CSalePaySystemAction::GetList(
			array(),
			array(
				"PAY_SYSTEM_ID" => $arResult["PAY_SYSTEM_ID"],
				"PERSON_TYPE_ID" => $arResult["PERSON_TYPE"]
			),
			false,
			false,
			array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING")
		);
		if ($arPaySysAction = $dbPaySysAction->Fetch())
		{
			$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
			if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
			{
				if ($arPaySysAction["NEW_WINDOW"] != "Y")
				{
					CSalePaySystemAction::InitParamArrays($arOrder, $arResult['ORDER_ID'], $arPaySysAction["PARAMS"]);

					$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

					$pathToAction = str_replace("\\", "/", $pathToAction);
					while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
						$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

					if (file_exists($pathToAction))
					{
						if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
							$pathToAction .= "/payment.php";

						$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
					}

					if(strlen($arPaySysAction["ENCODING"]) > 0)
					{
						define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
						AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
						function ChangeEncoding($content)
						{
							global $APPLICATION;
							header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
							$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
							$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
						}
					}
				}
			}
			$arResult["PAY_SYSTEM"] = $arPaySysAction;
		}
	}
	$arResult["ORDER"] = $arOrder;
	$arDateInsert = explode(" ", $arOrder["DATE_INSERT"]);
	if (is_array($arDateInsert) && count($arDateInsert) > 0)
		$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arDateInsert[0];
	else
		$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arOrder["DATE_INSERT"];

	if(CModule::IncludeModule("statistic"))
	{
		$event1 = "eStore";
		$event2 = "order_confirm";
		$event3 = $arResult["ORDER"]["ID"];

		$e = $event1."/".$event2."/".$event3;

		if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
		{
			CStatistic::Set_Event($event1, $event2, $event3);
			$_SESSION["ORDER_EVENTS"][] = $e;
		}
	}

	foreach(GetModuleEvents("sale", "OnSaleComponentOrderComplete", true) as $arEvent)
		ExecuteModuleEventEx($arEvent, Array($arOrder["ID"], $arOrder));
}
//------------------ STEP 6 ----------------------------------------------
elseif ($arResult["CurrentStep"] == 7)
{
	$arOrder = false;
	if ($bUseAccountNumber) // supporting ACCOUNT_NUMBER or ID in the URL
	{
		$dbOrder = CSaleOrder::GetList(
			array("DATE_UPDATE" => "DESC"),
			array(
				"LID" => SITE_ID,
				"USER_ID" => IntVal($USER->GetID()),
				"ACCOUNT_NUMBER" => $ID
			)
		);

		if ($arOrder = $dbOrder->GetNext())
		{
			$arResult["ORDER_ID"] = $arOrder["ID"];
		}
	}

	if (!$arOrder)
	{
		$dbOrder = CSaleOrder::GetList(
			array("DATE_UPDATE" => "DESC"),
			array(
				"LID" => SITE_ID,
				"USER_ID" => IntVal($USER->GetID()),
				"ID" => $ID
			)
		);

		$arOrder = $dbOrder->GetNext();
	}

	if ($arOrder)
	{
		if (IntVal($arOrder["PAY_SYSTEM_ID"]) > 0)
		{
			$dbPaySysAction = CSalePaySystemAction::GetList(
				array(),
				array(
					"PAY_SYSTEM_ID" => $arOrder["PAY_SYSTEM_ID"],
					"PERSON_TYPE_ID" => $arOrder["PERSON_TYPE_ID"]
				),
				false,
				false,
				array("NAME", "ACTION_FILE", "NEW_WINDOW", "PARAMS", "ENCODING")
			);
			if ($arPaySysAction = $dbPaySysAction->Fetch())
			{
				$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
				if (strlen($arPaySysAction["ACTION_FILE"]) > 0)
				{
					if ($arPaySysAction["NEW_WINDOW"] != "Y")
					{
						CSalePaySystemAction::InitParamArrays($arOrder, $ID, $arPaySysAction["PARAMS"]);

						$pathToAction = $_SERVER["DOCUMENT_ROOT"].$arPaySysAction["ACTION_FILE"];

						$pathToAction = str_replace("\\", "/", $pathToAction);
						while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
							$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

						if (file_exists($pathToAction))
						{
							if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
								$pathToAction .= "/payment.php";

							$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
						}

						if(strlen($arPaySysAction["ENCODING"]) > 0)
						{
							define("BX_SALE_ENCODING", $arPaySysAction["ENCODING"]);
							AddEventHandler("main", "OnEndBufferContent", "ChangeEncoding");
							function ChangeEncoding($content)
							{
								global $APPLICATION;
								header("Content-Type: text/html; charset=".BX_SALE_ENCODING);
								$content = $APPLICATION->ConvertCharset($content, SITE_CHARSET, BX_SALE_ENCODING);
								$content = str_replace("charset=".SITE_CHARSET, "charset=".BX_SALE_ENCODING, $content);
							}
						}
					}
				}
				$arResult["PAY_SYSTEM"] = $arPaySysAction;
			}
		}

		$arResult["ORDER"] = $arOrder;

		$arDateInsert = explode(" ", $arOrder["DATE_INSERT"]);
		if (is_array($arDateInsert) && count($arDateInsert) > 0)
			$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arDateInsert[0];
		else
			$arResult["ORDER"]["DATE_INSERT_FORMATED"] = $arOrder["DATE_INSERT"];

		if(CModule::IncludeModule("statistic"))
		{
			$event1 = "eStore";
			$event2 = "order_confirm";
			$event3 = $arResult["ORDER"]["ID"];

			$e = $event1."/".$event2."/".$event3;

			if(!is_array($_SESSION["ORDER_EVENTS"]) || (is_array($_SESSION["ORDER_EVENTS"]) && !in_array($e, $_SESSION["ORDER_EVENTS"]))) // check for event in session
			{
				CStatistic::Set_Event($event1, $event2, $event3);
				$_SESSION["ORDER_EVENTS"][] = $e;
			}
		}

		foreach(GetModuleEvents("sale", "OnSaleComponentOrderComplete", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, Array($arOrder["ID"], $arOrder));

	}
}
//------------------------------------------------------------------------

$arResult["DISCOUNT_PRICE_ALL"] = $DISCOUNT_PRICE_ALL;
$arResult["DISCOUNT_PRICE_ALL_FORMATED"] = SaleFormatCurrency($DISCOUNT_PRICE_ALL, $allCurrency);

// получим список компаний и адресов пользователя
$arResult["USERS_COMPANIES"] = array();
$arSelect = Array("ID", "NAME");
$arFilter = Array(
	"IBLOCK_ID" => USERS_COMPANY_IBLOCK_ID,
	"PROPERTY_USER" => $USER->GetID()
);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while ( $ob = $res->GetNext() )
{
	$arResult["USERS_COMPANIES"][] = $ob["NAME"];
}

$arResult["USERS_ADDRESSES"] = array();
$arSelect = Array("ID", "NAME", "PROPERTY_*", "IBLOCK_ID");
$arFilter = Array(
	"IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
	"PROPERTY_USER" => $USER->GetID()
);
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
while ( $ob = $res->GetNextElement() )
{
	$arFields = $ob->GetFields();
	$arProperties = $ob->GetProperties();
	$arResult["USERS_ADDRESSES"][$arFields['ID']]['FIELDS'] = $arFields;
	$arResult["USERS_ADDRESSES"][$arFields['ID']]['PROPS'] = $arProperties;
}

$this->IncludeComponentTemplate();
?>