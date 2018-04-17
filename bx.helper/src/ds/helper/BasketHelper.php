<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */


class BasketHelper
{

    /**
     * есть ли товар в корзине ?
     * @param $productId
     * @return string
     */
    public static function isProductBasket($productId)
    {
        CModule::IncludeModule('iblock');
        CModule::IncludeModule('main');
        CModule::IncludeModule("sale");
        CModule::IncludeModule("catalog");

        $arID = array();

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "PRODUCT_PROVIDER_CLASS")
        );
        while ($arItems = $dbBasketItems->Fetch())
        {
            if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"])
            {
                CSaleBasket::UpdatePrice($arItems["ID"],
                    $arItems["CALLBACK_FUNC"],
                    $arItems["MODULE"],
                    $arItems["PRODUCT_ID"],
                    $arItems["QUANTITY"],
                    "N",
                    $arItems["PRODUCT_PROVIDER_CLASS"]
                );
                $arID[] = $arItems["ID"];
            }
        }

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "ID" => $arID,
                "ORDER_ID" => "NULL",
                "PRODUCT_ID" => $productId
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY",
                "CAN_BUY", "PRICE", "WEIGHT", "PRODUCT_PROVIDER_CLASS", "NAME")
        );

        $isBasket = false;
        if ($arItems = $dbBasketItems->Fetch()) {
            $isBasket = true;
        }

        return $isBasket;

    }



    /**
     * количество товара в корзине
     * @param $productId
     * @return int
     */
    public static function productQuantityBasket($productId)
    {

        CModule::IncludeModule('iblock');
        CModule::IncludeModule('main');
        CModule::IncludeModule("sale");
        CModule::IncludeModule("catalog");


        $arID = array();

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL"
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "PRODUCT_PROVIDER_CLASS")
        );
        while ($arItems = $dbBasketItems->Fetch())
        {
            if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"])
            {
                CSaleBasket::UpdatePrice($arItems["ID"],
                    $arItems["CALLBACK_FUNC"],
                    $arItems["MODULE"],
                    $arItems["PRODUCT_ID"],
                    $arItems["QUANTITY"],
                    "N",
                    $arItems["PRODUCT_PROVIDER_CLASS"]
                );
                $arID[] = $arItems["ID"];
            }
        }

        $dbBasketItems = CSaleBasket::GetList(
            array(
                "NAME" => "ASC",
                "ID" => "ASC"
            ),
            array(
                "ID" => $arID,
                "ORDER_ID" => "NULL",
                "PRODUCT_ID" => $productId
            ),
            false,
            false,
            array("ID", "CALLBACK_FUNC", "MODULE",
                "PRODUCT_ID", "QUANTITY", "DELAY",
                "CAN_BUY", "PRICE", "WEIGHT", "PRODUCT_PROVIDER_CLASS", "NAME")
        );
        $quantityInBasket = 0;
        if ($arItems = $dbBasketItems->Fetch()) {
            $quantityInBasket = $arItems["QUANTITY"];
        }

        return $quantityInBasket;

    }


}