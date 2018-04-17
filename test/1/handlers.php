<?

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler(
    'sale',
    'OnSaleBasketSaved',
    array('ChangeBasketClass', "OnSaleBasketSavedHandler")
);

$eventManager->addEventHandler(
    'catalog',
    'OnGetOptimalPrice',
    array('ChangeBasketClass', "OnGetOptimalPriceHandler")
);


$_SESSION["arBasketItemsFinal"] = array();

class ChangeBasketClass
{

    protected static $strActionIBlockCode = "actions";
    protected static $N = 1;
    protected static $X = 1;

    protected static function getAction()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        $arActionElements = array();

        $cache = Bitrix\Main\Data\Cache::createInstance();
        $cache_time = 86400;
        $cache_id = 'ChangeBasketClass_getAction_' . self::$strActionIBlockCode;
        $cache_path = '/ChangeBasketClass_getAction/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->getVars();
            if (is_array($res["arActionElements"]) && (count($res["arActionElements"]) > 0))
                $arActionElements = $res["arActionElements"];
        }

        if (empty($arActionElements)) {

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);


            $rsElement = \CIBlockElement::GetList(array(),
                array("IBLOCK_CODE" => self::$strActionIBlockCode),
                false, false,
                array("PROPERTY_N", "PROPERTY_X", "DATE_ACTIVE_TO", "DATE_ACTIVE_FROM", "ACTIVE", "IBLOCK_ID"));
            while ($arElement = $rsElement->GetNext()) {
                $arActionElements[] = $arElement;
                $CACHE_MANAGER->RegisterTag("Element_" . md5(serialize($arElement)));
            };

            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->startDataCache();
                $cache->endDataCache(array("arActionElements" => $arActionElements));
            }

        }


        $today= strtotime(date("d.m.Y h:i:s"));
        /**на случай, если акции несколько, выберем первую активную в данный момент
        проще конечно было бы сделать фильтрацию по DATE_ACTIVE в GetList и не использовать этот цикл,
        но в таком случае выборка активного в данный момент элемента ("ACTIVE" => "Y", "DATE_ACTIVE" => "Y") не увяжется с кешированием
        а если отказаться от кэширования, то дёргать элемент их инфоблока при каждом срабатывании события - слишком затратно по
        нагрузке */
        foreach($arActionElements as $arActionElement){

            if( ($arActionElement["ACTIVE"] == "Y") &&
                (($arActionElement["DATE_ACTIVE_FROM"] <= $today) && ($today >= $arActionElement["DATE_ACTIVE_TO"])) ){
                //текущая дата лежит в интервале между датой начала
                //и датой окончания акции, и акция активна
                self::$N = $arActionElement["PROPERTY_N_VALUE"];
                self::$X = $arActionElement["PROPERTY_X_VALUE"];
                break;
            }

        }

        return true;

    }


    public static function OnSaleBasketSavedHandler(\Bitrix\Main\Event $event)
    {

        //Действие 1

        \Bitrix\Main\Loader::includeModule('sale');
        /**каждый N-й товар будет стоить X [текущая валюта]
        $N = 2;
        $X = 1;*/
        self::getAction();//1)

        $basket = $event->getParameter("ENTITY");

        /**получаем корзину*/
        $arBasketItems = array();
        $arBasketItemsOriginal = array();
        $basketItems = $basket->getBasketItems();

        /**один элемент массива - один экземпляр товара*/
        foreach ($basketItems as $basketItem) {
            $prices = \CCatalogProduct::GetByIDEx($basketItem->getProductId());
            $price = $prices['PRICES'][CCatalogGroup::GetBaseGroup()["ID"]]['PRICE'];

            $arBasketItemsOriginal[$basketItem->getProductId()] = array(
                "Id" => $basketItem->getId(),
                "ProductId" => $basketItem->getProductId(),
                //"Price" => $basketItem->getPrice(),
                "Price" => $price,
                "Quantity" => $basketItem->getQuantity()
            );
            for ($i = 0; $i < $basketItem->getQuantity(); $i++) {
                $arBasketItems[] = array(
                    "Id" => $basketItem->getId(),
                    "ProductId" => $basketItem->getProductId(),
                    //"Price" => $basketItem->getPrice(),
                    "Price" => $price,
                );
            }
        };


        /**сортировка по возрастанию цены*/
        uasort($arBasketItems, function ($a, $b) {
            return $a['Price'] - $b['Price'];
        });

        $numCountItemsHavingXPrice = ceil(count($arBasketItems) / self::$N);
        $arBasketItems = array_values($arBasketItems);


        for ($i = 0; $i < $numCountItemsHavingXPrice; $i++) {
            $arBasketItems[$i]["Price"] = floatval(self::$X);
        }

        $_SESSION["arBasketItemsFinal"] = array();

        foreach ($arBasketItems as $basketItem) {
            if (isset($_SESSION["arBasketItemsFinal"][$basketItem["ProductId"]])) {
                $_SESSION["arBasketItemsFinal"][$basketItem["ProductId"]] += $basketItem["Price"];
            } else {
                $_SESSION["arBasketItemsFinal"][$basketItem["ProductId"]] = $basketItem["Price"];
            }
        }

        foreach ($_SESSION["arBasketItemsFinal"] as $basketItemFinalProductId => &$basketItemFinalPrice) {
            $basketItemFinalPrice = $basketItemFinalPrice / $arBasketItemsOriginal[$basketItemFinalProductId]["Quantity"];
        }

        return true;

    }

    public static function OnGetOptimalPriceHandler($productId,
                                                    $quantity = 1,
                                                    $arUserGroups = [],
                                                    $renewal = "N",
                                                    $arPrices = [],
                                                    $siteID = false,
                                                    $arDiscountCoupons = false)
    {

        \Bitrix\Main\Loader::includeModule('catalog');
        if (isset($_SESSION["arBasketItemsFinal"][$productId])) {
            $price = $_SESSION["arBasketItemsFinal"][$productId];
        } else {
            $prices = \CCatalogProduct::GetByIDEx($productId);
            $price = $prices['PRICES'][CCatalogGroup::GetBaseGroup()["ID"]]['PRICE'];
        }

        return [
            'PRICE' => [
                "ID" => $productId,
                'CATALOG_GROUP_ID' => "",
                'PRICE' => intval($price),
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'ELEMENT_IBLOCK_ID' => $productId,
                'VAT_INCLUDED' => "Y",
            ],
            'DISCOUNT' => [
                'VALUE' => '',
                'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
            ],
        ];

    }
}