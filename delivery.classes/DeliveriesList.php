<?php
use Bitrix\Main\Loader;

class DeliveriesList
{

    protected static $deliveries = array();

    function __construct($location)
    {
        Loader::includeModule('sale');
        $arFilter = array("LID" => SITE_ID);
        if ((string)$location !== '') {
            $arFilter['LOCATION'] = $location;
        }
        $dbRes = CSaleDelivery::GetList(
            array("SORT" => "ASC", "NAME" => "ASC"), $arFilter);

        while ($arRes = $dbRes->Fetch()) {
            self::addDelivery(new Delivery($arRes));
        }

    }

    public static function addDelivery(Delivery $delivery)
    {
        self::$deliveries[$delivery->getId()] = $delivery->getFields();
    }

    public static function getDelivery($id)
    {
        return isset(self::$deliveries[$id]) ? self::$deliveries[$id] : null;
    }

    public static function removeDelivery($id)
    {
        if (array_key_exists($id, self::$deliveries)) {
            unset(self::$deliveries[$id]);
        }
    }

    public function getAll()
    {
        return self::$deliveries;
    }

    public function getAllOrderByCode()
    {
        $arDeliveries = self::$deliveries;
        $deliveries = array();
        foreach ($arDeliveries as $value) {
            if ($value['IS_OPT']) {
                $deliveries['OPT'][$value['CODE']] = $value;
            }
            if ($value['IS_RETAIL']) {
                $deliveries['RETAIL'][$value['CODE']] = $value;
            }
        }

        if(!empty($deliveries['RETAIL'])){
            usort($deliveries['RETAIL'], array('DeliveriesList', 'sort'));
        }

        if(!empty($deliveries['OPT'])){
            usort($deliveries['OPT'], array('DeliveriesList', 'sort'));
        }

        return $deliveries;
    }

    public function deleteDeniedDeliveries($arDeniedDelivery = array())
    {
        if (!empty($arDeniedDelivery)) {
            foreach (self::$deliveries as $key => $delivery) {
                if (in_array($delivery['ID'], $arDeniedDelivery)) {
                    self::removeDelivery($delivery['ID']);
                }
            }
        }

        return $this;
    }

    public static function getDeniedDeliveries($isAvailableShopLogisticDelivery)
    {
        $deniedDelivery = array();

        if (!(bool)$isAvailableShopLogisticDelivery) {
            $deniedDelivery = [
                PICK_UP_FREE_POINT_ID, PICK_UP_PAID_POINT_ID, TO_DOOR_WHOLESALE_FREE_ID, TO_DOOR_WHOLESALE_PAID_ID,
                TO_DOOR_RETAIL_FREE_ID, TO_DOOR_RETAIL_PAID_ID
            ];
        }
        return $deniedDelivery;
    }


    public static function getOptDeliveries()
    {
        return array(STOREHOUSE_ID, TRANSPORT_COMPANY_ID, TO_DOOR_WHOLESALE_PAID_ID, TO_DOOR_WHOLESALE_FREE_ID);
    }

    public static function getRetailDeliveries()
    {
        return array(STOREHOUSE_ID, PICK_UP_PAID_POINT_ID, TO_DOOR_RETAIL_FREE_ID, MAIL_ID, MAIL_PAID_ID,
            TO_DOOR_RETAIL_PAID_ID, PICK_UP_FREE_POINT_ID);
    }

    public static function sort($a, $b)
    {
        $sortA = (int)$a['SORT'];
        $sortB = (int)$b['SORT'];
        if ($sortA === $sortB) {
            return 0;
        } else {
            return ($sortA > $sortB) ? 1 : -1;
        }
    }
}