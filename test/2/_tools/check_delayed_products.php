<?
use \Bitrix\Main\Loader;

define(NO_KEEP_STATISTIC, true);
define(NOT_CHECK_PERMISSIONS, true);
define(BX_BUFFER_USED, true);

define(EVENT_NAME, "CHECK_DELAYED_PRODUCTS");
define(LID, "s1");

if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = '../..';
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

set_time_limit(0);

while (ob_get_level()) {
    ob_end_flush();
}

Loader::includeModule("iblock");
Loader::includeModule("catalog");
Loader::includeModule("sale");

$arFilter = array();
$arUserIDs = array();
$arOrderIDUserIDMap = array();

/**
 * корзина может существовать и более 30 дней, но нас интересуют товары корзины, которые попали в отложеные
 * за последние 30 дней. изменение корзины так же происходит в том числе и при добавлении в отложенные,
 * поэтому нужно получить все брошенные корзины, изменённые за последние 30 дней, но не факт, что абсолютно во всех корзинах из
 * данной выборки будут обязательно присутствовать отложенные товары.
 */

/** Получим дату, до которой нам необходимо получить отложенные товары. */
$filterDate = (new \DateTime())->modify('-30 days')->format('d.m.Y');
$arFilter[">=DATE_UPDATE"] = $filterDate;
$dbLeaveBasketResultList = \CSaleBasket::GetLeave(
    array(),
    $arFilter,
    false,
    false
);

$arBasketAllDelayedItems = array();
$arAllDelayedItemsInBasketByUserID = array();
$arAllOrderedItemsInBasketByUserID = array();
$arUsersIDsHaveDelayed = array();
$arFuserIDs = array();
$arLIDs = array();
$arAllDelayedItemsFormattedInBasketByUserID = array();
$arUsersDateHaveBasket = array();


while ($arBasket = $dbLeaveBasketResultList->Fetch()) {
    if (intval($arBasket['USER_ID']) == 0) continue;
    $arFuserIDs[] = $arBasket["FUSER_ID"];
    $arLIDs[] = $arBasket["LID"];

    /** данные пользователей, имеющих брошенные корзины. будут использоваться при отправке писем */
    $arUsersDateHaveBasket[$arBasket['USER_ID']] = array( //
        "USER_NAME" => $arBasket["USER_NAME"],
        "USER_LAST_NAME" => $arBasket["USER_LAST_NAME"],
        "USER_EMAIL" => $arBasket["USER_EMAIL"]
    );

}

$arFilterBasket = Array(
    "ORDER_ID" => false,
    "FUSER_ID" => $arFuserIDs,
    "LID" => $arLIDs,
    "DELAY" => 'Y',
);

$dbBasketItems = \CSaleBasket::GetList(
    array("ID" => "ASC"),
    $arFilterBasket,
    false,
    false,
    array("ID", "NAME", "PRICE", "CURRENCY", "QUANTITY", "MEASURE_NAME", "PRODUCT_ID", "USER_ID")
);


while ($arBasketItem = $dbBasketItems->Fetch()) {

    $arUsersIdsHaveDelayed[] = $arBasketItem['USER_ID'];

    /** все отложенные товары за последние 30 дней */
    $arAllDelayedItemsInBasketByUserID[$arBasketItem['USER_ID']][] = $arBasketItem["PRODUCT_ID"];//

    $strItemFormattedText = "";
    $strItemFormattedText .= $arBasketItem["NAME"] . " - ";
    $strItemFormattedText .= $arBasketItem["QUANTITY"] . " x ";
    $strItemFormattedText .= SaleFormatCurrency($arBasketItem["PRICE"], $arBasketItem["CURRENCY"]);
    $arAllDelayedItemsFormattedInBasketByUserID[$arBasketItem['USER_ID']][$arBasketItem["PRODUCT_ID"]] = $strItemFormattedText;//

}


/**
 * $arAllDelayedItemsFormattedInBasketByUserID - это форматированные описания позиций, будут использованы при отправке
 * писем пользователям
 *
 * $arUsersIdsHaveDelayed - id пользователей, которые имеют в своих корзинах товары, отложенные за последние 30 дней
 */

$arUsersIdsHaveDelayed = array_unique($arUsersIdsHaveDelayed);

$dbSalesResultList = \CSaleOrder::GetList(false, array("USER_ID" => $arUsersIdsHaveDelayed, ">=DATE_INSERT" => $filterDate));//
while ($arSaleItem = $dbSalesResultList->Fetch()) {
    $arOrderIDUserIDMap[$arSaleItem["ID"]] = $arSaleItem["USER_ID"];

    /** все заказы пользователей, у которых есть товар, созданные за последние 30 дней */
    $arAllSalesIDs[] = $arSaleItem["ID"];
}


$arFilterBasket = Array(
    "ORDER_ID" => $arAllSalesIDs
);

$dbBasketItems = \CSaleBasket::GetList(
    array("ID" => "ASC"),
    $arFilterBasket,
    false,
    false,
    array("PRODUCT_ID", "ORDER_ID", "USER_ID")//
);

while ($arBasketItem = $dbBasketItems->Fetch()) {
    /**
     * здесь $arBasketItem['USER_ID'] имеет неправильное значение , поэтому используется
     * массив $arOrderIDUserIDMap
     */

    /** все товары из заказов, созданных за последние 30 дней */
    $arAllOrderedItemsInBasketByUserID[$arOrderIDUserIDMap[$arBasketItem['ORDER_ID']]][] = $arBasketItem["PRODUCT_ID"];

}


$arResultItemsByUserID = array();

foreach($arUsersIdsHaveDelayed as $userIDHaveDelayed){

    /** получаем разницу между отложенными и заказанными товарами для каждого пользователя*/
    $arResultItemsByUserID[$userIDHaveDelayed] = array_diff($arAllDelayedItemsInBasketByUserID[$userIDHaveDelayed], $arAllOrderedItemsInBasketByUserID[$userIDHaveDelayed]);

}

$arResultItemsByUserID = array_diff($arResultItemsByUserID, array(''));

/**
 * шлём письма
 */
foreach($arResultItemsByUserID as $resultUserID => $arResultItems){
    if(empty($arResultItems)) continue;

    $arrFieldsEmail = array(
        'EMAIL' => $arUsersDateHaveBasket[$resultUserID]['USER_EMAIL'],
        "NAME" => $arUsersDateHaveBasket[$resultUserID]['USER_NAME'],
        "LAST_NAME" => $arUsersDateHaveBasket[$resultUserID]['USER_LAST_NAME'],
        "PRODUCT_LIST" => implode("\r\n", $arAllDelayedItemsFormattedInBasketByUserID[$resultUserID])
    );
    CEvent::SendImmediate(EVENT_NAME, SITE_ID, $arrFieldsEmail);

}

