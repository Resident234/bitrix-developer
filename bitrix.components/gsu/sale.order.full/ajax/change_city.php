<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
/*header('Content-Type: application/json');
$res = "";
$result = array();
$result["hid"] = 1;
if (!CModule::IncludeModule("sale"))
    return;
$arDeliveryFilter = array(
	'ACTIVE'=>'Y',"ID"=>PICK_UP_POINT_ID
);
$dbDelivery = CSaleDelivery::GetList(
	array("SORT"=>"ASC", "NAME"=>"ASC"),
	$arDeliveryFilter
);
$arPickPointDelivery =array();
while ($arDelivery = $dbDelivery->GetNext())
{
	$arPickPointDelivery = $arDelivery;
}
$pickupList = null;
$pickupProperty = null;
if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/delivery/delivery_shoplogistics.php"))
	include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/delivery/delivery_shoplogistics.php");
$pickupList = CDeliveryShoplogistic::getPickupListByBitrixLocation(
    $APPLICATION->get_cookie("CITY_ID")
);

if(null !== $pickupList){
    $result["hid"] = 0;
    $res = $res.'<div class="popup map_popup" style="display: none;">
        <div id="map"></div>
    </div>
     <div class="b-form__row_delivery_description">'
		.$arPickPointDelivery['DESCRIPTION'].
	'</div>
    <div class="b-delivery__punkt">
        <b>Выберите пункт на карте</b> или из списка
        <div class="b-delivery__punkt__sel">
            <select>';
                foreach($pickupList as &$pickup){
                    if(!empty($pickup['CITY_NAME']) && false === strpos($pickup['ADDRESS'], $pickup['CITY_NAME'])) {
                        $pickup['ADDRESS'] = 'г. '.$pickup['CITY_NAME'].', '.$pickup['ADDRESS'];
                    }
                    else if(!empty($pickup['REGION_NAME']) && false === strpos($pickup['ADDRESS'], $pickup['REGION_NAME'])) {
                        $pickup['ADDRESS'] = $pickup['REGION_NAME'].', '.$pickup['ADDRESS'];
                    }
                    $res = $res.'<option value="'.$pickup['CODE'].'">'.$pickup['ADDRESS'].'</option>';
                }
    $res = $res.'</select></div></div>';

    $lat = 0;
    $lon = 0;
    $count = 0;
    foreach($pickupList as $pickup1)
    {
        $lat += $pickup1["LATITUDE"];
        $lon += $pickup1["LOGITUDE"];
        $count++;
    }
    $mid_lat = $lat/$count;
    $mid_lon = $lon/$count;
    $res = $res."<script>
        var myMap;
        // Дождёмся загрузки API и готовности DOM.
        ymaps.ready(init);

        function init () {
            myMap = new ymaps.Map('map_wrapper2', {
                center: [".$mid_lat.", ".$mid_lon."],
                zoom: 9,
                controls: ['smallMapDefaultSet'],
                scrollZoom: false,
                type: 'yandex#map'
            });

            myMap.controls.add('zoomControl');";

            foreach($pickupList as $pickup2) {
                $res = $res."myPlacemark = new ymaps.Placemark([".$pickup2["LATITUDE"].", ".$pickup2["LOGITUDE"]."], {
                hintContent: '<b>Адрес: </b>".$pickup2['ADDRESS']."<br><b>Номер телефона: </b>".$pickup2['PHONE']."<br><b>Время работы: </b>".$pickup2['WORKTIME']."<br><b>Как проехать: </b>".$pickup2['PROEZD_INFO']."<br><b>Срок доставки: </b>".$pickup2['SROK_DOSTAVKI']." дн.'
                }, {
                    click:function(){console.log(2222)}
                });
                myPlacemark.events.add('click', function (e) {";
                    $res = $res.'$(".b-delivery__punkt__sel select option").each(function() {';
                    $res = $res."$(this).attr('selected', false);
                    });";
                    $res = $res.'$(".b-delivery__punkt__sel select").find("option[value='.$pickup2["CODE"].']").attr("selected", "selected");';
                    $res = $res.'$(".b-delivery__punkt__sel select")';
                    $res = $res.".trigger('refresh');";
                    $res = $res.'$(".b-delivery__punkt__sel select")';
                    $res = $res.".trigger('change');";
                    $res = $res."console.log(".$pickup2["CODE"].");
                });
                myMap.geoObjects.add(myPlacemark);";
            }
    $res = $res."}</script>";

    $res = $res.'<div class="b-map">
        <div id="map_wrapper2" class="b-map__map">
        </div>
    </div>';
    $i=0;
    foreach($pickupList as $pickup3){
        $res = $res.'<div class="pickup-description pickup'.$pickup3['CODE'].'" ';
            if(!$i){
                $res = $res.'style="display: block;"';
            }
        $res = $res.'>';
            if(!$i){
                $first["PHONE"] = $pickup3['PHONE'];
                $first["ADDRESS"]  = $pickup3['ADDRESS'];
            }
			$info = "";
			if(strlen($pickup3['ADDRESS']))
			{
				$info.="Адрес: {$pickup3['ADDRESS']}, ";
			}
			if(strlen($pickup3['PHONE']))
			{
				$info.="Номер телефона: {$pickup3['PHONE']}, ";
			}
			if(strlen($pickup3['WORKTIME']))
			{
				$info.="Время работы: {$pickup3['WORKTIME']}, ";
			}
			if(strlen($pickup3['PROEZD_INFO']))
			{
				$info.="Как проехать: {$pickup3['PROEZD_INFO']}, ";
			}
			if(strlen($pickup3['SROK_DOSTAVKI']))
			{
				$info.="Срок доставки: {$pickup3['SROK_DOSTAVKI']} дн., ";
			}
			$info = substr($info, 0, -2);
        $res = $res.'<input type="hidden" class="pickupDelivery" value="'.$info.'">
        	<input class="ADDRESS" type="hidden" value="'.$pickup3['ADDRESS'].'">
            <input class="PHONE" type="hidden" value="'.$pickup3['PHONE'].'">
            <b>Адрес: </b>'.$pickup3['ADDRESS'].'<br>
            <b>Номер телефона: </b>'.$pickup3['PHONE'].'<br>
            <b>Время работы: </b>'.$pickup3['WORKTIME'].'<br>
            <b>Как проехать: </b>'.$pickup3['PROEZD_INFO'].'<br>
            <b>Срок доставки: </b>'.$pickup3['SROK_DOSTAVKI'].' дн.
        </div>';
        $i++;
    }

    $db_props = CSaleOrderProps::GetList(
        array("SORT" => "ASC"),
        array(
            "PROPS_GROUP_ID" => DELIVERY_POINT_RETAIL_BUYER_ID
        ),
        false,
        false,
        array()
    );

    while ($props = $db_props->Fetch())
    {
        $res = $res.'<input class="RESULT_'.$props['CODE'].'" type="hidden" maxlength="250" size="'.$props["SIZE1"].'" value="'.$first[$arProperties['CODE']].'" name="ORDER_PROP_'.$props["ID"].'">';
    }
}
$result["res"] = $res;
echo json_encode($result);*/
$APPLICATION->IncludeComponent(
	"peppers:sale.order.full",
	".default",
	array(
		"PATH_TO_BASKET" => "/personal/order/",
		"PATH_TO_PERSONAL" => "/personal/",
		"PATH_TO_AUTH" => "/auth/",
		"PATH_TO_PAYMENT" => "/personal/order/payment/",
		"PATH_TO_ORDER"=>"/personal/order/make/",
		"ALLOW_PAY_FROM_ACCOUNT" => "Y",
		"SHOW_MENU" => "N",
		"USE_AJAX_LOCATIONS" => "N",
		"SHOW_AJAX_DELIVERY_LINK" => "Y",
		"CITY_OUT_LOCATION" => "Y",
		"COUNT_DELIVERY_TAX" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"SET_TITLE" => "Y",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "Y",
		"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
		"SEND_NEW_USER_NOTIFY" => "Y",
		"DELIVERY_NO_SESSION" => "N",
		"PROP_3" => array(
		),
		"PROP_2" => "",
		"PATH_TO_STATUS" => "#",
		"COMPONENT_TEMPLATE" => ".default",
		"PROP_4" => array(
		)
	),
	false
);?>