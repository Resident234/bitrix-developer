<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER, $DB;

use \Bitrix\Main,
    \Bitrix\Main\Localization\Loc as Loc,
    Bitrix\Main\Loader,
    Bitrix\Main\Config\Option,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    Bitrix\Sale,
    Bitrix\Sale\Order,
    Bitrix\Sale\DiscountCouponsManager,
    Bitrix\Main\Context,
    Helpers\Common;

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");



for ($i = 0; $i < 20; $i++) {

    $DB->StartTransaction();

    $user = new CUser;
    $arFields = Array(
        "NAME" => "Name" . $i,
        "LAST_NAME" => "Lastname" . $i,
        "EMAIL" => "name" . $i . "@mail.ru",
        "LOGIN" => "login" . $i . rand(0, PHP_INT_MAX),
        "LID" => "ru",
        "ACTIVE" => "Y",
        "GROUP_ID" => array(1),
        "PASSWORD" => "123456",
        "CONFIRM_PASSWORD" => "123456"
    );

    $userID = $user->Add($arFields);

    if (!$userID){ $DB->Rollback(); die; }

    $siteId = \Bitrix\Main\Context::getCurrent()->getSite();


    $currencyCode = Option::get('sale', 'default_currency', 'RUB');

    DiscountCouponsManager::init();


    $order = Order::create($siteId, $userID);//\CSaleUser::GetAnonymousUserID()

    $order->setPersonTypeId(1);

    //$basket = Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), $siteId)->getOrderableItems();
    $basket = \Bitrix\Sale\Basket::create(\Bitrix\Main\Context::getCurrent()->getSite());

    for($iBasketItem = 0; $iBasketItem < 100; $iBasketItem++){
        $item = $basket->createItem('catalog', rand(100, 300), $basketCode = null);
        $item->setFields(array(
            'QUANTITY' => rand(10, 30),
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
            'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
        ));

    }

    //$basketItems = $basket->getBasketItems();
    //foreach ($basketItems as $basketItem) {

    //}


    $order->setBasket($basket);

    /*Shipment*/
    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    $shipment->setField('CURRENCY', $order->getCurrency());
    foreach ($order->getBasket() as $item)
    {
        $shipmentItem = $shipmentItemCollection->createItem($item);
        $shipmentItem->setQuantity($item->getQuantity());
    }
    $arDeliveryServiceAll = Delivery\Services\Manager::getRestrictedObjectsList($shipment);
    $shipmentCollection = $shipment->getCollection();

    if (!empty($arDeliveryServiceAll)) {
        reset($arDeliveryServiceAll);
        $deliveryObj = current($arDeliveryServiceAll);

        if ($deliveryObj->isProfile()) {
            $name = $deliveryObj->getNameWithParent();
        } else {
            $name = $deliveryObj->getName();
        }

        $shipment->setFields(array(
            'DELIVERY_ID' => $deliveryObj->getId(),
            'DELIVERY_NAME' => $name,
            'CURRENCY' => $order->getCurrency()
        ));

        $shipmentCollection->calculateDelivery();
    }
    /**/

    /*Payment*/
    $arPaySystemServiceAll = array();
    $paySystemId = 1;
    $paymentCollection = $order->getPaymentCollection();

    $remainingSum = $order->getPrice() - $paymentCollection->getSum();
    if ($remainingSum > 0 || $order->getPrice() == 0)
    {
        $extPayment = $paymentCollection->createItem();
        $extPayment->setField('SUM', $remainingSum);
        $arPaySystemServices = PaySystem\Manager::getListWithRestrictions($extPayment);

        $arPaySystemServiceAll += $arPaySystemServices;

        if (array_key_exists($paySystemId, $arPaySystemServiceAll))
        {
            $arPaySystem = $arPaySystemServiceAll[$paySystemId];
        }
        else
        {
            reset($arPaySystemServiceAll);

            $arPaySystem = current($arPaySystemServiceAll);
        }

        if (!empty($arPaySystem))
        {
            $extPayment->setFields(array(
                'PAY_SYSTEM_ID' => $arPaySystem["ID"],
                'PAY_SYSTEM_NAME' => $arPaySystem["NAME"]
            ));
        }
        else
            $extPayment->delete();
    }
    /**/

    $order->doFinalAction(true);
    $propertyCollection = $order->getPropertyCollection();

    $emailProperty = getPropertyByCode($propertyCollection, 'EMAIL');
    $emailProperty->setValue($email);

    $phoneProperty = getPropertyByCode($propertyCollection, 'PHONE');
    $phoneProperty->setValue($phone);

    $order->setField('CURRENCY', $currencyCode);
    $order->setField('USER_DESCRIPTION', 'Комментарии пользователя');
    $order->setField('COMMENTS', 'Комментарии менеджера');

    $order->save();

    $orderId = $order->GetId();

    if (!$orderId){ $DB->Rollback(); die; }
    $DB->Commit();

    $arFUser = CSaleUser::GetList(array('USER_ID' => $userID));
    $basket = \Bitrix\Sale\Basket::loadItemsForFUser($arFUser["ID"], Bitrix\Main\Context::getCurrent()->getSite());//Sale\Fuser::getId()

    $arDelay = array("Y", "N");
    for($iBasketItem = 0; $iBasketItem < 100; $iBasketItem++){
        $item = $basket->createItem('catalog', rand(100, 300), $basketCode = null);
        $item->setFields(array(
            'QUANTITY' => rand(10, 30),
            'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            'LID' => \Bitrix\Main\Context::getCurrent()->getSite(),
            'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
            'DELAY' => $arDelay[rand(0, count($arDelay) - 1)]
        ));
        $item->save();

    }

    $basket->setFUserId($arFUser["ID"]);

    $basket->save();

}

function getPropertyByCode($propertyCollection, $code)  {
    foreach ($propertyCollection as $property)
    {
        if($property->getField('CODE') == $code)
            return $property;
    }
}



require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");