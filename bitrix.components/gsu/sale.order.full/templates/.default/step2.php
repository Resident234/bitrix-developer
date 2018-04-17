<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!function_exists('PrintPropsForm'))
{
	function PrintPropsForm($arSource=Array(), $PRINT_TITLE = "", $arParams)
	{
		global $USER,$APPLICATION;
		if (!empty($arSource))
		{
			foreach($arSource as $arProperties)
			{

				if($arProperties["PROPS_GROUP_ID"] == INFO_RETAIL_BUYER_ID || $arProperties["PROPS_GROUP_ID"] == INFO_WHOLESALE_BUYER_ID) {
					?>
					<?
					if($arProperties["TYPE"] == "CHECKBOX")
					{
						?>
						<div class="clear"></div>
						<div class="b-form__row b-form__row_check">
							<input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="ch_<?=$arProperties["ID"]?>" value="Y">
							<label for="ch_<?=$arProperties["ID"]?>"><?=$arProperties["NAME"]?></label>
							<?if($arProperties["CODE"] == "PERS_INFO"):?>
								<a class="b-form__pers-link open_popup" href="#rules">Соглашение на обработку персональных данных</a>
								<div class="b-popup" id="rules">
									<div class="b-popup__title">
										Соглашение об обработке<br>
										персональных данных</div>
									<div class="b-popup__text b-popup__text_large">
										<?global $APPLICATION;
										$APPLICATION->IncludeComponent(
											"bitrix:main.include",
											"",
											Array(
												"AREA_FILE_SHOW" => "file",
												"PATH" => "/include/rules.php",
												"EDIT_TEMPLATE" => ""
											)
										);?>
									</div>
									<p>
										<a class="btn btn_red fancy_close" href="#">Принять</a>
									</p>

								</div>
								<div class="b-form__row__input__error">
									Необходимо подтвердить согласие на обработку персональных данных
								</div>
							<?endif;?>
						</div>
						<?
					}
					elseif($arProperties["TYPE"] == "TEXT")
					{
						$dataId = "";

						if($USER->IsAuthorized()) {
							$rsUser = CUser::GetByID($USER->GetID());
							$arUser = $rsUser->Fetch();
							switch ($arProperties["CODE"]) {
								case "NAME":
									$arProperties["VALUE"] = $arUser["NAME"];
									break;
								case "SURNAME":
									$arProperties["VALUE"] = $arUser["LAST_NAME"];
									break;
								case "MIDDLE_NAME":
									$arProperties["VALUE"] = $arUser["SECOND_NAME"];
									break;
								case "PHONE":
									$arProperties["VALUE"] = $arUser["PERSONAL_PHONE"];
									break;
							}
						}
						?>
						<?if($arProperties["ID"]==NAME_COMPANY_ID||$arProperties["ID"]==NAME_COMPANY_CONSIGNEE_ID):?>


						<div class="b-form__row">
							<div class="b-form__row__name">
								<?=$arProperties["NAME"]?>
							</div>
							<div class="b-form__row__input">
								<select class="company_select_<?=$arProperties["ID"]?>">
									<?foreach($arParams["USER_COMPANIES"] as $company):?>
										<option value="<?=$company?>"><?=$company?></option>
									<?endforeach?>
									<?if(!count($arParams["USER_COMPANIES"])):?>
										<option value="">Добавьте компанию в личном кабинете</option>
									<?else:?>
										<option value="" data-id="<?=$arProperties["ID"]?>">Другое</option>
									<?endif?>
								</select>
							</div>
							<div class="b-form__row__textarea">
									<textarea class="b-form__row__input" rows="1"
											  data-val="<?= $arProperties['ID'] ?>"
											  placeholder="Введите наименование компании"
											  style="display: none">
									 </textarea>
								<div class="b-form__row__input__error">
									Нужно заполнить!
								</div>
							</div>
						</div>

						<input type="hidden"
							   maxlength="250"
							   size="<?=$arProperties["SIZE1"]?>"
							   value="<?=$arProperties["VALUE"]?>"
							   name="<?=$arProperties["FIELD_NAME"]?>"
							   class="company_input_<?=$arProperties["ID"]?>"
						/>
					<?else:?>
						<div class="b-form__row">
							<div class="b-form__row__name">
								<?=$arProperties["NAME"]?>
							</div>
							<div class="b-form__row__input <?if($arProperties["CODE"] == "EMAIL"){?>email_field<?}?> ">
								<input <? if ($arProperties["CODE"] == "PHONE"||
								$arProperties['CODE']=="EMAIL"|| $arProperties['CODE']=="NAME")
									   {
									   ?>class="required<?
								if($arProperties['CODE']=="PHONE")
								{?>
		                                            input_phone
	                                            <?}?>
	                                            "
									<?} ?>
									   type="text" maxlength="250" size="<?= $arProperties["SIZE1"] ?>"
									   value="<?= $arProperties["VALUE"] ?>" name="<?= $arProperties["FIELD_NAME"] ?>"
									   data-id="<?= $dataId ?>">
								<div class="b-form__row__input__error">
									Нужно заполнить!
								</div>
							</div>
						</div>
					<?endif;?>
						<?
					}
					elseif($arProperties["TYPE"] == "SELECT")
					{
						?>
						<div class="b-form__row">
							<div class="b-form__row__name">
								<?=$arProperties["NAME"]?>
							</div>
							<div class="b-form__row__input">
								<select name="<?=$arProperties["FIELD_NAME"]?>">
									<?
									foreach($arProperties["VARIANTS"] as $arVariants)
									{
										?>
										<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
										<?
									}
									?>
								</select>
								<div class="b-form__row__input__error">
									Нужно заполнить!
								</div>
							</div>
						</div>
						<?if($arProperties["CODE"] == "NAME_COMPANY_CONSIGNEE"):?>
						<div class="clear"></div>
					<?endif;?>
						<?
					}
					elseif ($arProperties["TYPE"] == "MULTISELECT")
					{
						?>
						<select multiple name="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
							<?
							foreach($arProperties["VARIANTS"] as $arVariants)
							{
								?>
								<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
								<?
							}
							?>
						</select>
						<?
					}
					elseif ($arProperties["TYPE"] == "TEXTAREA")
					{
						?>
						<textarea rows="<?=$arProperties["SIZE2"]?>" cols="<?=$arProperties["SIZE1"]?>" name="<?=$arProperties["FIELD_NAME"]?>"><?=$arProperties["VALUE"]?></textarea>
						<?
					}
					elseif ($arProperties["TYPE"] == "LOCATION")
					{
						$value = 0;
						if(intval($APPLICATION->get_cookie("CITY_ID")))
						{
							$value = $APPLICATION->get_cookie("CITY_ID");
						}
						elseif(intval($_SESSION['CITY_ID']))
						{
							$value=$_SESSION['CITY_ID'];
						}
						else
						{
							foreach ($arProperties["VARIANTS"] as $arVariant)
							{
								if ($arVariant["SELECTED"] == "Y")
								{
									$value = $arVariant["ID"];
									break;
								}
							}
						}?>
						<input type="hidden" name="<?=$arProperties['FIELD_NAME']?>" value="<?=$value?>"
						/>
					<?}
					elseif ($arProperties["TYPE"] == "RADIO")
					{
						foreach($arProperties["VARIANTS"] as $arVariants)
						{
							?>
							<input type="radio" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["ID"]?>" value="<?=$arVariants["VALUE"]?>"<?if($arVariants["CHECKED"] == "Y") echo " checked";?>> <label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["ID"]?>"><?=$arVariants["NAME"]?></label><br />
							<?
						}
					}

					if (strlen($arProperties["DESCRIPTION"]) > 0)
					{
						?><br /><small><?echo $arProperties["DESCRIPTION"] ?></small><?
					}
					?>
				<?  }
			}
			return true;
		}
		return false;
	}
}
?>

<div class="b-order__tip">Обязательны для заполнения: Имя, телефон, e-mail</div>

<div class="b-order__step step2">
	<div class="b-form">
		<?$bPropsPrinted = PrintPropsForm($arResult["PRINT_PROPS_FORM"]["USER_PROPS_N"], GetMessage("SALE_INFO2ORDER"), $arParams);?>
	</div>
</div>