<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
?>
<div class="js-template">
    <!--js-template-->
    <? if (!empty($arResult['DELIVERIES'])): ?>
        <div class="b-content__tab-links tab_control tab-buttons no_top" data-tabwrapper="#tab-main7">
            <? $i = 0; ?>
            <? foreach ($arResult['TABS'] as $key => $tab): ?>
                <a href="javascript:void(0)" <? if ($i == 0): ?>class="current"<? endif; ?>
                   data-tab="tab<?= $key ?>"><?= $tab['NAME'] ?></a>
                <? $i++; ?>
            <? endforeach; ?>
        </div>
        <div class="b-content__tab-wrapper tab-wrapper " id="tab-main7" data-url="<?= $arResult['AJAX_PATH']; ?>">
            <? $i = 0; ?>
            <? foreach ($arResult['TABS'] as $key => $tab): ?>
                <div class="tab tab<?= $key; ?> <? if ($i == 0): ?>current<? endif; ?>">
                    <form>
                        <div class="b-order__city">
                            Ваш город*:
                            <div class="b-order__city-fsel">
                                <a href="#city_change" class="open_popup"><?= getCurCity(); ?></a>
                            </div>
                        </div>
                        <div class="b-delivery" id="delivery_form">
                            <?
                            foreach ($arResult["DELIVERIES"][$tab['CODE']] as $arDelivery) {
                                $arDelivery["FIELD_NAME"] = "DELIVERY_ID";
                                switch ($arDelivery['ID']) {
                                    case TO_DOOR_WHOLESALE_PAID_ID:
                                    case TO_DOOR_WHOLESALE_FREE_ID:
                                    case TO_DOOR_RETAIL_FREE_ID:
                                    case TO_DOOR_RETAIL_PAID_ID:
                                        ?>
                                        <div class="b-delivery__item b-delivery__item__to-door">
                                            <div class="b-delivery__title">
                                                <div class="b-delivery__radio">
                                                    <input type="radio" name="<?= $arDelivery['FIELD_NAME'] ?>"
                                                           id="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery['ID'] ?>"
                                                           value="<?= $arDelivery['ID'] ?>">
                                                </div>
                                                <label for="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery['ID'] ?>">
                                                    <?= $arDelivery['NAME'] ?> в городе <b>
                                                        <?= $_SESSION['PLACE'][$APPLICATION->get_cookie('CITY_ID')] ?>
                                                    </b>
                                                </label>
                                            </div>
                                            <div class="b-delivery__wrap">
                                                <div class="b-form">
                                                    <div class="b-form__row_delivery_description">
                                                        <?= $arDelivery['DESC'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <? break;
                                    case STOREHOUSE_ID:
                                        ?>
                                        <div class="b-delivery__item b-delivery__item__storehouse">
                                            <div class="b-delivery__title">
                                                <div class="b-delivery__radio">
                                                    <input type="radio" name="<?= $arDelivery["FIELD_NAME"] ?>"
                                                           id="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                                           value="<?= $arDelivery["ID"] ?>">
                                                </div>
                                                <label for="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
                                                    <?= $arDelivery["NAME"] ?>
                                                </label>
                                            </div>
                                            <div class="b-delivery__wrap">
                                                <div class="b-form">
                                                    <div class="b-form__row_delivery_description">
                                                        <p><?= $arDelivery['DESC'] ?></p>
                                                    </div>
                                                    <div class="b-map">
                                                        <div id="map_wrapper_<?= $tab['CODE'] ?>" class="b-map__map">
                                                            <? $APPLICATION->IncludeComponent(
                                                                "bitrix:map.yandex.view",
                                                                "custom",
                                                                Array(
                                                                    "INIT_MAP_TYPE" => "MAP",
                                                                    "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.80103838684764;s:10:\"yandex_lon\";d:37.59314308633968;s:12:\"yandex_scale\";i:14;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.5923706101434;s:3:\"LAT\";d:55.80148255796552;s:4:\"TEXT\";s:48:\"Пункт выдачи заказов Dimobi.ru\";}}}",
                                                                    "MAP_WIDTH" => "655",
                                                                    "MAP_HEIGHT" => "334",
                                                                    "CONTROLS" => array(
                                                                        0 => "ZOOM",
                                                                        1 => "SMALLZOOM",
                                                                        2 => "MINIMAP",
                                                                        3 => "SCALELINE",
                                                                    ),
                                                                    "OPTIONS" => array(
                                                                        0 => "ENABLE_SCROLL_ZOOM",
                                                                        1 => "ENABLE_DBLCLICK_ZOOM",
                                                                        2 => "ENABLE_DRAGGING",
                                                                    ),
                                                                    "MAP_ID" => 'map_wrapper_' . $tab['CODE'],
                                                                    "DEV_MODE" => "Y",
                                                                ),
                                                                false
                                                            ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <? break;
                                    case MAIL_ID:
                                    case MAIL_PAID_ID:
                                        ?>
                                        <div class="b-delivery__item b-delivery__item__mail">
                                            <div class="b-delivery__title">
                                                <div class="b-delivery__radio">
                                                    <input type="radio" name="<?= $arDelivery["FIELD_NAME"] ?>"
                                                           id="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                                           value="<?= $arDelivery["ID"] ?>">
                                                </div>
                                                <label for="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
                                                    <?= $arDelivery["NAME"] ?>
                                                </label>
                                            </div>
                                            <div class="b-delivery__wrap">
                                                <input type="hidden" class="no_default"/>
                                                <div class="b-form">
                                                    <div class="b-form__row_delivery_description">
                                                        <?= $arDelivery['DESC'] ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <? break;
                                    case TRANSPORT_COMPANY_ID:
                                        ?>
                                        <? if (count($arResult['TRANSPORT_COMPANIES'])): ?>
                                        <div class="b-delivery__item b-delivery__item__transport">
                                            <div class="b-delivery__title">
                                                <div class="b-delivery__radio">
                                                    <input type="radio" name="<?= $arDelivery["FIELD_NAME"] ?>"
                                                           id="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                                           value="<?= $arDelivery["ID"] ?>">
                                                </div>
                                                <label for="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
                                                    <?= $arDelivery["NAME"] ?>
                                                </label>
                                            </div>
                                            <div class="b-delivery__wrap">
                                                <div class="b-form__row_delivery_description">
                                                    <?= $arDelivery['DESC'] ?>
                                                </div>
                                                <div class="b-form">
                                                    <div class="b-form__row">
                                                        <div class="b-form__row__name">
                                                            Доступные транспортные компании
                                                        </div>
                                                        <div class="b-form__row__input">
                                                            <select class="consignee_select_72" name="ORDER_PROP_72">
                                                                <? foreach ($arResult['TRANSPORT_COMPANIES'] as $company): ?>
                                                                    <option value="<?= $company['VALUE'] ?>">
                                                                        <?= $company['NAME'] ?>
                                                                    </option>
                                                                <? endforeach ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <? endif; ?>
                                        <? break;
                                    case PICK_UP_POINT_ID:
                                    case PICK_UP_FREE_POINT_ID:
                                        ?>
                                        <div class="b-delivery__item b-delivery__item__pick_up_point">
                                            <div class="b-delivery__title">
                                                <div class="b-delivery__radio">
                                                    <input type="radio" name="<?= $arDelivery["FIELD_NAME"] ?>"
                                                           id="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>"
                                                           value="<?= $arDelivery["ID"] ?>">
                                                </div>
                                                <label for="<?= $tab['CODE'] ?>_DELIVERY_ID_<?= $arDelivery["ID"] ?>">
                                                    <?= $arDelivery["NAME"] ?> в городе
                                                    <b><?= $_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")] ?></b>
                                                </label>
                                            </div>
                                            <div class="b-delivery__wrap">
                                                <div id="<?= $tab['CODE'] ?>_PICK_UP_POINT_DELIVERY">
                                                    <div class="b-form__row_delivery_description">
                                                        <?= $arDelivery['DESC']; ?>
                                                    </div>
                                                    <? if (null !== $arResult['PICKUP_LIST']): ?>
                                                        <div class="b-delivery__punkt">
                                                            <b>Выберите пункт на карте:</b>
                                                            <div class="b-delivery__punkt__sel" style="display: none">
                                                                <select>
                                                                    <? $prevPickupId = 0;
                                                                    foreach ($arResult['PICKUP_LIST'] as &$pickup):?>
                                                                        <?
                                                                        if ($pickup["CODE"] != $prevPickupId):
                                                                            if (!empty($pickup['CITY_NAME']) && false === strpos(
                                                                                    $pickup['ADDRESS'],
                                                                                    $pickup['CITY_NAME']
                                                                                )
                                                                            ) {
                                                                                $pickup['ADDRESS'] = 'г. ' . $pickup['CITY_NAME'] . ', ' . $pickup['ADDRESS'];
                                                                            } else {
                                                                                if (!empty($pickup['REGION_NAME']) && false === strpos(
                                                                                        $pickup['ADDRESS'],
                                                                                        $pickup['REGION_NAME']
                                                                                    )
                                                                                ) {
                                                                                    $pickup['ADDRESS'] = $pickup['REGION_NAME'] . ', ' . $pickup['ADDRESS'];
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <option
                                                                                data-lat="<?= $pickup['LATITUDE'] ?>"
                                                                                data-log="<?= $pickup['LOGITUDE'] ?>"
                                                                                data-address="<?= $pickup['ADDRESS'] ?>"
                                                                                value="<?= $pickup['CODE'] ?>"
                                                                                data-phone="<?= $pickup['PHONE'] ?>"
                                                                                data-worktime="<?= $pickup['WORKTIME'] ?>"
                                                                                data-proezd-info="<?= $pickup['PROEZD_INFO'] ?>"
                                                                                data-srok-dostavki="<?= $pickup['SROK_DOSTAVKI'] ?> дн."
                                                                                data-pickup-place-type="<?= $pickup['PICKUP_PLACE_TYPE'] ?>">
                                                                                <?= $pickup['ADDRESS'] ?>
                                                                            </option>
                                                                        <? endif; ?>
                                                                        <? $prevPickupId = $pickup["CODE"]; ?>
                                                                    <? endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="b-map">
                                                            <div
                                                                id="<?= $tab['CODE'] ?>_map_wrapper_<?= $arDelivery['ID'] ?>"
                                                                class="b-map__map pvz_map">
                                                            </div>
                                                        </div>
                                                        <? $i = 0; ?>
                                                        <? $prevPickupId = 0;
                                                        foreach ($arResult['PICKUP_LIST'] as $pickup3): ?>
                                                            <? if ($pickup3["CODE"] != $prevPickupId): ?>
                                                                <div
                                                                    class="pickup-description pickup<?= $pickup3['CODE'] ?>"
                                                                    <? if (!$i): ?>style="display: block;" <? endif; ?>>
                                                                    <input type="hidden" class="pvz-info"
                                                                           data-address="<?= $pickup3['ADDRESS'] ?>"
                                                                           data-phone="<?= $pickup3['PHONE'] ?>"
                                                                           data-worktime="<?= $pickup3['WORKTIME'] ?>"
                                                                           data-proezd_info="<?= $pickup3['PROEZD_INFO'] ?>"
                                                                           data-srok_dostavki="<?= $pickup3['SROK_DOSTAVKI'] ?>"
                                                                           data-pickup_place_type="<?= $pickup3['PICKUP_PLACE_TYPE'] ?>">
                                                                    <? if ((string)$pickup3['ADDRESS'] !== ''): ?>
                                                                        <b>Адрес: </b><?= $pickup3['ADDRESS'] ?>
                                                                    <? endif; ?>
                                                                    <? if ((string)$pickup3['PHONE'] !== ''): ?>
                                                                        <br><b>Номер
                                                                            телефона: </b><?= $pickup3['PHONE'] ?>
                                                                    <? endif; ?>
                                                                    <? if ((string)$pickup3['WORKTIME'] !== ''): ?>
                                                                        <br><b>Время
                                                                            работы: </b><?= $pickup3['WORKTIME'] ?>
                                                                    <? endif; ?>
                                                                    <? if ((string)$pickup3['PROEZD_INFO'] !== ''): ?>
                                                                        <br><b>Как
                                                                            проехать: </b><?= $pickup3['PROEZD_INFO'] ?>
                                                                    <? endif; ?>
                                                                    <? if ((string)$pickup3['SROK_DOSTAVKI']): ?>
                                                                        <br><b>Срок
                                                                            доставки: </b><?= $pickup3['SROK_DOSTAVKI'] ?> дн.
                                                                    <? endif; ?>
                                                                </div>
                                                                <? $i++; ?>
                                                            <? endif;
                                                            ?>
                                                            <? $prevPickupId = $pickup3["CODE"]; ?>
                                                        <? endforeach; ?>
                                                    <? endif ?>
                                                </div>
                                            </div>
                                        </div>
                                        <? break;
                                }
                            }
                            ?>
                        </div>
                    </form>
                    <div class="preloader preloader_delivery"></div>
                </div>
                <? $i++; ?>
            <? endforeach; ?>
        </div>
    <? endif; ?>
    <!--js-template-->
</div>