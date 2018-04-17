<?php

class BasketHelper
{
    private static $_instance = null;
    private $_basket = [];

    private function __construct()
    {
        CModule::IncludeModule("sale");
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
            array("ID")
        );
        $result = [];
        while ($arItems = $dbBasketItems->Fetch()) {
            $result[$arItems["ID"]] = $arItems["ID"];
        }
        $this->_basket = $result;
    }

    function getBasket()
    {
        return $this->_basket;
    }

    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}