<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;
?>
<div class="b-order__tip">Для расчета стоимости доставки выбор города обязателен</div>
<div class="b-order__step step3">
	<div class="b-order__city">
		Ваш город*:
		<div class="b-order__city-fsel">
			<a href="#city_change" class="open_popup"><?= getCurCity(); ?></a>
		</div>
	</div>
	<? if (!empty($arResult['DELIVERY'])): ?>
		<?$first=true;?>
		<div class="b-delivery">
			<?
			foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
			{
				?>
				<div class="b-delivery__item">
					<div class="b-delivery__title">
						<div class="b-delivery__radio">
							<input type="radio" name="<?= $arDelivery["FIELD_NAME"] ?>"
								   id="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>" value="<?= $arDelivery["ID"] ?>">
						</div>
						<label for="ID_DELIVERY_ID_<?= $arDelivery["ID"] ?>"><?= $arDelivery["NAME"] ?></label>
					</div>
					<div class="b-delivery__wrap">
						<?
						switch($arDelivery['ID'])
						{
							case TO_DOOR_WHOLESALE_PAID_ID:
							case TO_DOOR_WHOLESALE_FREE_ID:?>
								<input type="hidden" class="default"/>
							<?case TO_DOOR_RETAIL_FREE_ID:
							case TO_DOOR_RETAIL_PAID_ID:?>
								<div class="b-form">
									<div class="b-form__row_delivery_description">
										<?= $arDelivery['DESCRIPTION'] ?>
									</div>
									<? $defaultAddress = reset($arResult['USERS_ADDRESSES']); ?>
									<? foreach ($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"] as $arProperties): ?>
										<? if ($arProperties["PROPS_GROUP_ID"] == DELIVERY_TO_DOOR_RETAIL_BUYER_ID
											|| $arProperties["PROPS_GROUP_ID"] == DELIVERY_TO_DOOR_WHOLESALE_BUYER_ID
										): ?>
											<? if (($arProperties["ID"] == ADDRESS_ID || $arProperties['ID'] == 75)): ?>
												<?
												if ($USER->IsAuthorized())
												{
													?>
													<div class="b-form__row choose_address"
														<? if ($_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")] != "Москва"):?>
															style="display: none;"
														<?endif; ?>>
														<div class="b-form__row__name">
															<?= $arProperties["NAME"] ?>
														</div>
														<div class="b-form__row__input" style="width: 790px;">
															<select class="address_select_<?= $arProperties["ID"] ?>">
																<? foreach ($arResult["USERS_ADDRESSES"] as $key => $address): ?>
																	<option
																		value="<?= $address['FIELDS']['NAME'] ?>"
																		<? foreach ($arResult['USERS_ADDRESSES'][$key]['PROPS'] as $arProp): ?>
																			<? if ($arProp['PROPERTY_TYPE'] == "S"): ?>
																				data-<?= $arProp['CODE'] ?>="<?= $arProp['VALUE'] ?>"
																			<? endif; ?>
																		<? endforeach; ?>
																	>
																		<?= $address['FIELDS']['NAME'] ?>
																	</option>
																<? endforeach ?>
																<option value="other">Другой адрес</option>
															</select>
														</div>
													</div>
													<input type="hidden"
														   maxlength="250"
														   size="<?= $arProperties["SIZE1"] ?>"
														   value="<?= $arProperties["VALUE"] ?>"
														   name="<?= $arProperties["FIELD_NAME"] ?>"
														   class="address_input_<?= $arProperties["ID"] ?>"
													/>
													<?
												}
												?>
											<? else: ?>
												<div class="b-form__row"
													<? if ($arProperties['ID'] == 46 || $arProperties['ID'] == 52): ?>
														style="clear: both"
													<? endif; ?>>
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<? if ($arProperties["TYPE"] == "TEXT")
													{
														?>
														<div class="b-form__row__input email_field ">
															<input <? if ($arProperties['CODE'] == 'REQ')
																   {
																   ?>class="REQ"<? } ?> type="text"
																   size="<?= $arProperties["SIZE1"] ?>"
																<? if (strlen($arProperties["VALUE"])): ?>
																	value="<?= $arProperties['VALUE'] ?>"
																<? else: ?>
																	<? if ($_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")] == "Москва" &&
																		$USER->IsAuthorized()
																	): ?>
																		<? if (($arProperties['ID'] == 46 || $arProperties['ID'] == 52 && strlen($defaultAddress['PROPS']['CITY']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['CITY']['VALUE'] ?>"
																		<? elseif (($arProperties['ID'] == 47 || $arProperties['ID'] == 53 && strlen($defaultAddress['PROPS']['STREET']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['STREET']['VALUE'] ?>"
																		<? elseif (($arProperties['ID'] == 48 || $arProperties['ID'] == 54 && strlen($defaultAddress['PROPS']['HOME']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['HOME']['VALUE'] ?>"
																		<? elseif (($arProperties['ID'] == 49 || $arProperties['ID'] == 55 && strlen($defaultAddress['PROPS']['BUILDING']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['BUILDING']['VALUE'] ?>"
																		<? elseif (($arProperties['ID'] == 50 || $arProperties['ID'] == 56 && strlen($defaultAddress['PROPS']['BLOCK']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['BLOCK']['VALUE'] ?>"
																		<? elseif (($arProperties['ID'] == 51 || $arProperties['ID'] == 57 && strlen($defaultAddress['PROPS']['APARTMENT']['VALUE']))): ?>
																			value="<?= $defaultAddress['PROPS']['APARTMENT']['VALUE'] ?>"
																		<? endif; ?>
																	<? else: ?>
																		<? if (($arProperties['ID'] == 46 || $arProperties['ID'] == 52) && strlen($_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")])): ?>
																			value="<?= $_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")]; ?>"
																		<? endif; ?>
																	<? endif; ?>
																<? endif ?>
																   name="<?= $arProperties["FIELD_NAME"] ?>"
																<? if (($arProperties['ID'] == 46 || $arProperties['ID'] == 52) && strlen($_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")])): ?>
																	data-value="<?= $_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")]; ?>"
																<? endif; ?>
															>
															<div class="b-form__row__input__error">
																Нужно заполнить!
															</div>
														</div>
														<?
													} elseif ($arProperties["TYPE"] == "TEXTAREA")
													{
														?>
														<div class="b-form__row__input">
															<textarea
																style="margin: 0px; width: 790px; height: 50px; min-height: 0"
																name="<?= $arProperties["FIELD_NAME"] ?>"
																<? if (intval($arProperties['SIZE2'])): ?>rows="<?= $arProperties['SIZE2'] ?><? endif; ?>">
																<?= $arProperties["VALUE"] ?>
															</textarea>
														</div>
													<? } ?>
												</div>
											<? endif; ?>
										<? endif; ?>
									<? endforeach; ?>
									<div class="clear"></div>
								</div>
								<?break;
							case STOREHOUSE_ID:?>
								<input type="hidden" class="default"/>
								<div class="b-form__row_delivery_description">
									<?= $arDelivery['DESCRIPTION'] ?>
								</div>
								<a class="b-delivery__show_map" href="#">Показать на карте</a>
								<div class="b-map">
									<div id="map_wrapper1" class="b-map__map">
										<? $APPLICATION->IncludeComponent("bitrix:map.yandex.view", "custom", Array(
											"INIT_MAP_TYPE" => "MAP",
											"MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:55.80103838684764;s:10:\"yandex_lon\";d:37.59314308633968;s:12:\"yandex_scale\";i:14;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:37.5923706101434;s:3:\"LAT\";d:55.80148255796552;s:4:\"TEXT\";s:48:\"Пункт выдачи заказов Dimobi.ru\";}}}",
											"MAP_WIDTH" => "700",
											"MAP_HEIGHT" => "400",
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
											"MAP_ID" => "map_wrapper1",
											"DEV_MODE"=>"Y",
										),
											false
										); ?>
									</div>
								</div>
								<?break;
							case MAIL_ID:?>
								<input type="hidden" class="no_default"/>
								<div class="b-form">
									<div class="b-form__row_delivery_description">
										<?= $arDelivery['DESCRIPTION'] ?>
									</div>
									<? foreach ($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"] as $arProperties): ?>
										<? if ($arProperties["PROPS_GROUP_ID"] == DELIVERY_MAIL_RETAIL_BUYER_ID): ?>
											<div class="b-form__row">
												<div class="b-form__row__name">
													<?= $arProperties["NAME"] ?>
												</div>
												<? if ($arProperties["TYPE"] == "TEXT")
												{
													?>
													<div class="b-form__row__input email_field ">
														<input <? if ($arProperties['CODE'] == 'REQ')
															   {
															   ?>class="REQ"<? } ?> type="text" maxlength="250"
															   size="<?= $arProperties["SIZE1"] ?>"
															   value="<?= $arProperties["VALUE"] ?>"
															   name="<?= $arProperties["FIELD_NAME"] ?>">

														<div class="b-form__row__input__error">
															Нужно заполнить!
														</div>
													</div>
													<?
												} ?>
											</div>
										<? endif; ?>
									<? endforeach; ?>
									<div class="clear"></div>
								</div>
								<?break;
							case TRANSPORT_COMPANY_ID:?>
								<input type="hidden" class="no_default"/>
								<div class="b-form__row_delivery_description">
									<?= $arDelivery['DESCRIPTION'] ?>
								</div>
								<div class="b-form">
									<? foreach ($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"] as $arProperties): ?>
										<? if ($arProperties["PROPS_GROUP_ID"] == DELIVERY_TRANSPORT_COMPANY_WHOLESALE_BUYER_ID): ?>
											<input type="hidden" class="otherTransportCompany"
												   value="<?= OTHER_TRANSPORT_COMPANY_XML_ID ?>"/>
											<input type="hidden" class="transportCompany"
												   value="<?= ORDER_TRANSPORT_COMAPNY_ID ?>"/>
											<? if ($arProperties["TYPE"] == "TEXT")
											{
												?>
												<? if ($arProperties["ID"] == CONSIGNEE_ID): ?>
												<div class="b-form__row">
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<div class="b-form__row__input">
														<select class="consignee_select_<?= $arProperties["ID"] ?>">
															<? foreach ($arResult["USERS_COMPANIES"] as $company): ?>
																<option
																	value="<?= $company ?>"><?= $company ?></option>
															<? endforeach ?>
															<option value="other">Другая компания</option>
														</select>
													</div>
												</div>
												<input type="hidden"
													   maxlength="250"
													   size="<?= $arProperties["SIZE1"] ?>"
													   value="<?= $arProperties["VALUE"] ?>"
													   name="<?= $arProperties["FIELD_NAME"] ?>"
													   class="consignee_input_<?= $arProperties["ID"] ?>"
												/>
											<? elseif ($arProperties["ID"] == COMMENT_TO_DELIVERY_ID): ?>
												<div class="b-form__row textearea_order">
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<div class="b-form__row__input email_field ">
														<input <? if ($arProperties['CODE'] == 'REQ')
															   {
															   ?>class="REQ"<? } ?> type="text" maxlength="250"
															   size="<?= $arProperties["SIZE1"] ?>"
															   value="<?= $arProperties["VALUE"] ?>"
															   name="<?= $arProperties["FIELD_NAME"] ?>">
														<div class="b-form__row__input__error">
															Нужно заполнить!
														</div>
													</div>
												</div>
											<? else: ?>
												<div class="b-form__row">
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<div class="b-form__row__input email_field ">
														<input <? if ($arProperties['CODE'] == 'REQ')
															   {
															   ?>class="REQ"<? } ?> type="text" maxlength="250"
															   size="<?= $arProperties["SIZE1"] ?>"
															   value="<?= $arProperties["VALUE"] ?>"
															   name="<?= $arProperties["FIELD_NAME"] ?>">

														<div class="b-form__row__input__error">
															Нужно заполнить!
														</div>
													</div>
												</div>
											<? endif; ?>
												<?
											} elseif ($arProperties["TYPE"] == "SELECT")
											{
												?>
												<div class="b-form__row">
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<div class="b-form__row__input">
														<select name="<?= $arProperties["FIELD_NAME"] ?>">
															<?
															foreach ($arProperties["VARIANTS"] as $arVariants)
															{
																?>
																<option
																	value="<?= $arVariants["VALUE"] ?>"<? if ($arVariants["SELECTED"] == "Y") echo " selected"; ?>><?= $arVariants["NAME"] ?></option>
																<?
															}
															?>
														</select>
														<div class="b-form__row__input__error">
															Нужно заполнить!
														</div>
													</div>
												</div>
												<? if ($arProperties["CODE"] == "REQ"): ?>
												<div class="clear"></div>
											<?endif;
											} elseif ($arProperties["TYPE"] == "TEXTAREA")
											{
												?>
												<div class="b-form__row">
													<div class="b-form__row__name">
														<?= $arProperties["NAME"] ?>
													</div>
													<div class="b-form__row__input">
													<textarea
														name="<?= $arProperties["FIELD_NAME"] ?>"
														<? if (intval($arProperties['SIZE1'])): ?>cols="<?= $arProperties['SIZE1'] ?>"<? endif; ?>
														<? if (intval($arProperties['SIZE2'])): ?>rows="<?= $arProperties['SIZE2'] ?>"<? endif; ?>>
														<?= $arProperties["VALUE"] ?>
													</textarea>
													</div>
												</div>
											<? } ?>
										<? endif; ?>
									<? endforeach; ?>
								</div>
								<?break;
							case PICK_UP_POINT_ID:?>
								<div id="PICK_UP_POINT_DELIVERY">
									<div class="b-form__row_delivery_description">
										<?= $arDelivery['DESCRIPTION'] ?>
									</div>
									<? if (null !== $arResult['PICKUP_LIST']): ?>
										<div class="b-delivery__punkt">
											<b>Выберите пункт на карте</b> или из списка
											<div class="b-delivery__punkt__sel">
												<select>
													<? $prevPickupId = 0;
													foreach ($arResult['PICKUP_LIST'] as &$pickup):?>
														<?
														if ($pickup["CODE"] != $prevPickupId):
															if (!empty($pickup['CITY_NAME']) && false === strpos($pickup['ADDRESS'], $pickup['CITY_NAME']))
															{
																$pickup['ADDRESS'] = 'г. ' . $pickup['CITY_NAME'] . ', ' . $pickup['ADDRESS'];
															} else if (!empty($pickup['REGION_NAME']) && false === strpos($pickup['ADDRESS'], $pickup['REGION_NAME']))
															{
																$pickup['ADDRESS'] = $pickup['REGION_NAME'] . ', ' . $pickup['ADDRESS'];
															}
															?>
															<option data-lat="<?=$pickup['LATITUDE']?>"
																	data-log="<?=$pickup['LOGITUDE']?>"
																	data-address="<?=$pickup['ADDRESS']?>"
																	value="<?= $pickup['CODE'] ?>"
																	data-phone="<?=$pickup['PHONE']?>"
																	data-worktime="<?=$pickup['WORKTIME']?>"
																	data-proezd-info="<?=$pickup['PROEZD_INFO']?>"
																	data-srok-dostavki="<?=$pickup['SROK_DOSTAVKI']?> дн.">
																<?= $pickup['ADDRESS'] ?>
															</option>
														<? endif; ?>
														<? $prevPickupId = $pickup["CODE"]; ?>
													<? endforeach; ?>
												</select>
											</div>
										</div>
										<div class="b-map">
											<div id="map_wrapper2" class="b-map__map">
											</div>
										</div>
										<? $i = 0; ?>
										<? $prevPickupId = 0;
										foreach ($arResult['PICKUP_LIST'] as $pickup3): ?>
											<? if ($pickup3["CODE"] != $prevPickupId): ?>
												<div class="pickup-description pickup<?= $pickup3['CODE'] ?>"
													 <? if (!$i): ?>style="display: block;" <? endif;
												?>>
													<? if (!$i)
													{
														$first["PHONE"] = $pickup3['PHONE'];
														$first["ADDRESS"] = $pickup3['ADDRESS'];
													}
													$info = "";
													if (strlen($pickup3['ADDRESS']))
													{
														$info .= "Адрес: {$pickup3['ADDRESS']}, ";
													}
													if (strlen($pickup3['PHONE']))
													{
														$info .= "Номер телефона: {$pickup3['PHONE']}, ";
													}
													if (strlen($pickup3['WORKTIME']))
													{
														$info .= "Время работы: {$pickup3['WORKTIME']}, ";
													}
													if (strlen($pickup3['PROEZD_INFO']))
													{
														$info .= "Как проехать: {$pickup3['PROEZD_INFO']}, ";
													}
													if (strlen($pickup3['SROK_DOSTAVKI']))
													{
														$info .= "Срок доставки: {$pickup3['SROK_DOSTAVKI']} дн., ";
													}
													$info = substr($info, 0, -2); ?>
													<input type="hidden" class="pickupDelivery" value="<?= $info ?>">
													<input class="ADDRESS" type="hidden"
														   value="<?= $pickup3['ADDRESS'] ?>">
													<input class="PHONE" type="hidden" value="<?= $pickup3['PHONE'] ?>">
													<b>Адрес: </b><?= $pickup3['ADDRESS'] ?><br>
													<b>Номер телефона: </b><?= $pickup3['PHONE'] ?><br>
													<b>Время работы: </b><?= $pickup3['WORKTIME'] ?><br>
													<b>Как проехать: </b><?= $pickup3['PROEZD_INFO'] ?><br>
													<b>Срок доставки: </b><?= $pickup3['SROK_DOSTAVKI'] ?> дн.
												</div>
												<? $i++; ?>
											<? endif;
											?>
											<? $prevPickupId = $pickup3["CODE"]; ?>
										<? endforeach; ?>
										<? foreach ($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"] as $arProperties): ?>
											<? if ($arProperties["PROPS_GROUP_ID"] == DELIVERY_POINT_RETAIL_BUYER_ID): ?>
												<? if ($arProperties["TYPE"] == "TEXT")
												{?>
													<input class="RESULT_<?= $arProperties['CODE'] ?>" type="hidden"
														   maxlength="250" size="<?= $arProperties["SIZE1"] ?>"
														   value="<?= $first[$arProperties['CODE']] ?>"
														   name="<?= $arProperties["FIELD_NAME"] ?>">
												<?}?>
											<? endif; ?>
										<? endforeach; ?>
									<? endif ?>
								</div>
								<?break;
						}
						?>
					</div>
				</div>
				<?
			}
			?>
			<div class="b-form__row__input__error delivery__error">
				Необходимо выбрать службу доставки!
			</div>
		</div>
	<? endif; ?>
</div>