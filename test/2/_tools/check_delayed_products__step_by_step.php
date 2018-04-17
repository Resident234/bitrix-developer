<?
/**
 * т.к. между запусками скрипта данные хранятся в сесси, срипт нужно запускать примерно так:
 *
    $ wget --load-cookies /tmp/cookiefile --save-cookies /tmp/cookiefile \
    --keep-session-cookies -O /dev/null https://[путь к папке со скриптом]/_tools/check_delayed_products__step_by_step.php
 */

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;

define(NO_KEEP_STATISTIC, true);
define(NOT_CHECK_PERMISSIONS, true);
define(BX_BUFFER_USED, true);

define(EVENT_NAME, "CHECK_DELAYED_PRODUCTS");
define(LID, "s1");

$nPageSize = 10;

if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = '../..';
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$request = Application::getInstance()->getContext()->getRequest();
$uriString = $request->getRequestUri();
$uri = new Uri($uriString);

set_time_limit(0);

while (ob_get_level()) {
    ob_end_flush();
}
/**
 *  использовал при тестировании, когда нужно было начать с первого шага
    unset($_SESSION["DATA"]);
    unset($_SESSION["STEP"]);
    unset($_SESSION["NUM_PAGE"]);
*/

Loader::includeModule("iblock");
Loader::includeModule("catalog");
Loader::includeModule("sale");

if (!$_SESSION["STEP"]){

    /**  STEP CSaleBasket_GetLeave  - START - */
    if (!$_SESSION["NUM_PAGE"]){
        $iNumPage = 1;
    }else{
        $iNumPage = $_SESSION["NUM_PAGE"];
    }

    if($iNumPage == 1) {
        $_SESSION["DATA"]["arFilter"] = array();
        $_SESSION["DATA"]["arUserIDs"] = array();
        $_SESSION["DATA"]["arOrderIDUserIDMap"] = array();
    }

    /**
     * корзина может существовать и более 30 дней, но нас интересуют товары корзины, которые попали в отложеные
     * за последние 30 дней. изменение корзины так же происходит в том числе и при добавлении в отложенные,
     * поэтому нужно получить все брошенные корзины, изменённые за последние 30 дней, но не факт, что абсолютно во всех корзинах из
     * данной выборки будут обязательно присутствовать отложенные товары.
     */

    /** Получим дату, до которой нам необходимо получить отложенные товары. */
    $_SESSION["DATA"]["filterDate"] = (new \DateTime())->modify('-30 days')->format('d.m.Y');
    $_SESSION["DATA"]["arFilter"][">=DATE_UPDATE"] = $_SESSION["DATA"]["filterDate"];

    $dbLeaveBasketResultList = \CSaleBasket::GetLeave(
        array(),
        $arFilter,
        false,
        array('nPageSize' => $nPageSize, "iNumPage" => $iNumPage)//$iNumPage
    );

    if($iNumPage == 1) {
        $_SESSION["DATA"]["arBasketAllDelayedItems"] = array();
        $_SESSION["DATA"]["arAllDelayedItemsInBasketByUserID"] = array();
        $_SESSION["DATA"]["arAllOrderedItemsInBasketByUserID"] = array();
        $_SESSION["DATA"]["arUsersIDsHaveDelayed"] = array();
        $_SESSION["DATA"]["arFuserIDs"] = array();
        $_SESSION["DATA"]["arLIDs"] = array();
        $_SESSION["DATA"]["arAllDelayedItemsFormattedInBasketByUserID"] = array();
        $_SESSION["DATA"]["arUsersDateHaveBasket"] = array();
    }

    $isGoToNextStep = true;
    while ($arBasket = $dbLeaveBasketResultList->Fetch()) {
        if (intval($arBasket['USER_ID']) == 0) continue;

        if(in_array($arBasket["FUSER_ID"], $_SESSION["DATA"]["arFuserIDs"])){
            $isGoToNextStep = true;
            break;
        }

        $_SESSION["DATA"]["arFuserIDs"][] = $arBasket["FUSER_ID"];
        $_SESSION["DATA"]["arLIDs"][] = $arBasket["LID"];

        /** данные пользователей, имеющих брошенные корзины. будут использоваться при отправке писем */
        $_SESSION["DATA"]["arUsersDateHaveBasket"][$arBasket['USER_ID']] = array(
            "USER_NAME" => $arBasket["USER_NAME"],
            "USER_LAST_NAME" => $arBasket["USER_LAST_NAME"],
            "USER_EMAIL" => $arBasket["USER_EMAIL"]
        );
        $isGoToNextStep = false;

    }


    /**  STEP CSaleBasket_GetLeave  - END - */

    $_SESSION["NUM_PAGE"] = ++$iNumPage;

    if ($isGoToNextStep) {
        $_SESSION["STEP"] = "CSaleBasket-GetLeave";
        $_SESSION["NUM_PAGE"] = 1;
    }

}elseif($_SESSION["STEP"] == "CSaleBasket-GetLeave"){

    /**  STEP CSaleBasket_GetList  - START - */
    if (!$_SESSION["NUM_PAGE"]){
        $iNumPage = 1;
    }else{
        $iNumPage = $_SESSION["NUM_PAGE"];
    }

    $arFilterBasket = Array(
        "ORDER_ID" => false,
        "FUSER_ID" => $_SESSION["DATA"]["arFuserIDs"],
        "LID" => $_SESSION["DATA"]["arLIDs"],
        "DELAY" => 'Y',
    );

    $dbBasketItems = \CSaleBasket::GetList(
        array("ID" => "ASC"),
        $arFilterBasket,
        false,
        array('nPageSize' => $nPageSize, "iNumPage" => $iNumPage),
        array("ID", "NAME", "PRICE", "CURRENCY", "QUANTITY", "MEASURE_NAME", "PRODUCT_ID", "USER_ID")
    );
    /*
    */

    if(!$_SESSION["DATA"]["_BasketItemIDs"]) $_SESSION["DATA"]["_BasketItemIDs"] = array();

    $isGoToNextStep = true;
    while ($arBasketItem = $dbBasketItems->Fetch()) {

        if(in_array($arBasketItem["ID"], $_SESSION["DATA"]["_BasketItemIDs"])){
            $isGoToNextStep = true;
            break;
        }
        $_SESSION["DATA"]["_BasketItemIDs"][] = $arBasketItem["ID"];

        $_SESSION["DATA"]["arUsersIdsHaveDelayed"][] = $arBasketItem['USER_ID'];
        /** все отложенные товары за последние 30 дней */
        $_SESSION["DATA"]["arAllDelayedItemsInBasketByUserID"][$arBasketItem['USER_ID']][] = $arBasketItem["PRODUCT_ID"];

        $strItemFormattedText = "";
        $strItemFormattedText .= $arBasketItem["NAME"] . " - ";
        $strItemFormattedText .= $arBasketItem["QUANTITY"] . " x ";
        $strItemFormattedText .= SaleFormatCurrency($arBasketItem["PRICE"], $arBasketItem["CURRENCY"]);
        $_SESSION["DATA"]["arAllDelayedItemsFormattedInBasketByUserID"][$arBasketItem['USER_ID']][$arBasketItem["PRODUCT_ID"]] = $strItemFormattedText;
        $isGoToNextStep = false;
    }
    /**
     * $arAllDelayedItemsFormattedInBasketByUserID - это форматированные описания позиций, будут использованы при отравке
     * писем пользователям
     *
     * $arUsersIdsHaveDelayed - id пользователей, которые имеют в своих корзинах товары, отложенные за последние 30 дней
     */

    $_SESSION["DATA"]["arUsersIdsHaveDelayed"] = array_unique($_SESSION["DATA"]["arUsersIdsHaveDelayed"]);


    /**  STEP CSaleBasket_GetList  - END - */
    $_SESSION["NUM_PAGE"] = ++$iNumPage;

    if ($isGoToNextStep) {
        $_SESSION["STEP"] = "CSaleOrder-GetList";
        $_SESSION["NUM_PAGE"] = 1;
        unset($_SESSION["DATA"]["_BasketItemIDs"]);
    }

}elseif($_SESSION["STEP"] == "CSaleOrder-GetList"){

    /**  STEP CSaleOrder_GetList  - START - */
    if (!$_SESSION["NUM_PAGE"]){
        $iNumPage = 1;
    }else{
        $iNumPage = $_SESSION["NUM_PAGE"];
    }

    $dbSalesResultList = \CSaleOrder::GetList(false, array("USER_ID" => $_SESSION["DATA"]["arUsersIdsHaveDelayed"],
        ">=DATE_INSERT" => $_SESSION["DATA"]["filterDate"]), false, array('nPageSize' => 10, "iNumPage" => $iNumPage));

    $isGoToNextStep = true;
    while ($arSaleItem = $dbSalesResultList->Fetch()) {

        if(in_array($arSaleItem["ID"], $_SESSION["DATA"]["arAllSalesIDs"])){
            $isGoToNextStep = true;
            break;
        }

        $_SESSION["DATA"]["arOrderIDUserIDMap"][$arSaleItem["ID"]] = $arSaleItem["USER_ID"];

        /** все заказы пользователей, у которых есть товар, созданные за последние 30 дней */
        $_SESSION["DATA"]["arAllSalesIDs"][] = $arSaleItem["ID"];
        $isGoToNextStep = false;
    }


    /**  STEP CSaleOrder_GetList  - END - */
    $_SESSION["NUM_PAGE"] = ++$iNumPage;

    if ($isGoToNextStep) {
        $_SESSION["STEP"] = "CSaleBasket-GetList";
        $_SESSION["NUM_PAGE"] = 1;
    }

}elseif($_SESSION["STEP"] == "CSaleBasket-GetList"){

    /**  STEP CSaleBasket_GetList  - START - */
    if (!$_SESSION["NUM_PAGE"]){
        $iNumPage = 1;
    }else{
        $iNumPage = $_SESSION["NUM_PAGE"];
    }

    $arFilterBasket = Array(
        "ORDER_ID" => $_SESSION["DATA"]["arAllSalesIDs"]
    );

    $dbBasketItems = \CSaleBasket::GetList(
        array("ID" => "ASC"),
        $arFilterBasket,
        false,
        array('nPageSize' => $nPageSize, "iNumPage" => $iNumPage),
        array("ID", "PRODUCT_ID", "USER_ID", "ORDER_ID")
    );

    if(!$_SESSION["DATA"]["_BasketItemIDs"]) $_SESSION["DATA"]["_BasketItemIDs"] = array();

    $isGoToNextStep = true;
    while ($arBasketItem = $dbBasketItems->Fetch()) {

        if(in_array($arBasketItem["ID"], $_SESSION["DATA"]["_BasketItemIDs"])){
            $isGoToNextStep = true;
            break;
        }
        $_SESSION["DATA"]["_BasketItemIDs"][] = $arBasketItem["ID"];

        /**
         * здесь $arBasketItem['USER_ID'] имеет неправильное значение , поэтому используется
         * массив $arOrderIDUserIDMap
         */

        /** все товары из заказов, созданных за последние 30 дней */
        $_SESSION["DATA"]["arAllOrderedItemsInBasketByUserID"][$_SESSION["DATA"]["arOrderIDUserIDMap"][$arBasketItem['ORDER_ID']]][] = $arBasketItem["PRODUCT_ID"]; //$arBasketItem['USER_ID']
        $isGoToNextStep = false;
    }

    /**  STEP CSaleBasket_GetList  - END - */
    $_SESSION["NUM_PAGE"] = ++$iNumPage;

    if ($isGoToNextStep) {
        $_SESSION["STEP"] = "Delayed-Ordered-diff";
        $_SESSION["NUM_PAGE"] = 1;
    }

}elseif($_SESSION["STEP"] == "Delayed-Ordered-diff"){

    /**  STEP Delayed_Ordered_diff  - START - */

    $_SESSION["DATA"]["arResultItemsByUserID"] = array();
    foreach ($_SESSION["DATA"]["arUsersIdsHaveDelayed"] as $userIDHaveDelayed) {
        /** получаем разницу между отложенными и заказанными товарами для каждого пользователя*/
        $_SESSION["DATA"]["arResultItemsByUserID"][$userIDHaveDelayed] = array_diff($_SESSION["DATA"]["arAllDelayedItemsInBasketByUserID"][$userIDHaveDelayed],
            $_SESSION["DATA"]["arAllOrderedItemsInBasketByUserID"][$userIDHaveDelayed]);
    }

    $_SESSION["DATA"]["arResultItemsByUserID"] = array_diff($_SESSION["DATA"]["arResultItemsByUserID"], array(''));

    /**  STEP Delayed_Ordered_diff  - END - */

    $_SESSION["STEP"] = "SendImmediate";

}elseif($_SESSION["STEP"] == "SendImmediate"){

    /**  STEP SendImmediate  - START - */

    /**
     * шлём письма
     */
    foreach ($_SESSION["DATA"]["arResultItemsByUserID"] as $resultUserID => $arResultItems) {
        if (empty($arResultItems)) continue;

        $arrFieldsEmail = array(
            'EMAIL' => $_SESSION["DATA"]["arUsersDateHaveBasket"][$resultUserID]['USER_EMAIL'],
            "NAME" => $_SESSION["DATA"]["arUsersDateHaveBasket"][$resultUserID]['USER_NAME'],
            "LAST_NAME" => $_SESSION["DATA"]["arUsersDateHaveBasket"][$resultUserID]['USER_LAST_NAME'],
            "PRODUCT_LIST" => implode("\r\n", $_SESSION["DATA"]["arAllDelayedItemsFormattedInBasketByUserID"][$resultUserID])
        );
        CEvent::SendImmediate(EVENT_NAME, SITE_ID, $arrFieldsEmail);

    }


    unset($_SESSION["DATA"]);
    unset($_SESSION["STEP"]);
    unset($_SESSION["NUM_PAGE"]);
    /**  STEP SendImmediate  - END - */

}


