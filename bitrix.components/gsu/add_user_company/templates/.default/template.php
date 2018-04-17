<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

<div class="b-vacancies__item__name">Новая компания</div>
<div class="b-vacancies__item__wrap">
	<div class="b-form">
		<?foreach($arResult["FIELDS"] as $key=>$field):?>
			<div class="<?=$field["CSS_CLASS"]?>">
				<div class="b-form__row__name">
					<?if($arResult["FIELDS"][$key]["REQUIRED"]) echo "*"?>
					<?=$field["NAME"]?>
				</div>
				<div class="b-form__row__input email_field<?=$field["CLASS_ERROR"]?>">
					<?if($field["TYPE_FIELD"]=="SELECT"):?>
						<select name="" class="input_<?=$key?>">
							<?foreach($field["VALUE_LIST"] as $value):?>
								<option <?if($value["ID"]==$field["CURRENT_VALUE"]) echo "selected"?> value="<?=$value["ID"]?>"><?=$value["VALUE"]?></option>
							<?endforeach;?>
						</select>
					<?elseif($field["TYPE_FIELD"]=="INPUT"):?>
						<input class="input_<?=$key?> <?=$field["FIELD_ADD_CLASS"]?>"  type="text" value="<?=$field["VALUE"]?>" />
						<div class="b-form__row__input__error">
							<?=$field["ERROR_MESSAGE"]?>
						</div>
					<?elseif($field["TYPE_FIELD"]=="TEXTAREA"):?>
						<textarea class="input_<?=$key?> <?=$field["FIELD_ADD_CLASS"]?> height_70"><?=$field["VALUE"]?></textarea>
						<input class="input_<?=$key?> <?=$field["FIELD_ADD_CLASS"]?> hidden"  type="text" value="<?=$field["VALUE"]?>" />
						<div class="b-form__row__input__error">
							<?=$field["ERROR_MESSAGE"]?>
						</div>
					<?endif;?>
				</div>
			</div>

			<?if($key=="PHONE_ADD"):?>
				<div class="clear"></div>
				<div class="b-form__row">
					<p>Юридический адрес:</p>
				</div>
				<div class="clear"></div>
			<?elseif($key=="LEGAL_APARTMENT"):
				$randID = rand(0,1000);?>
				<div class="clear">
					<div class="b-form__row b-form__row_check">
						<span style="margin-right:20px;">Адрес доставки:</span>
						<input type="checkbox"  value="1" id="ch4" <?if($arResult["DELIVERY_EQ_LEGAL"]) echo "checked"?>  class="eq"/>
						<label for="ch4">Совпадает с юридическим</label>
						<a class="b-form__pers-link open_popup" href="#<?=$randID?>">Взять из моих адресов</a>

						<div class="b-popup "  id="<?=$randID?>">
							<?$APPLICATION->IncludeComponent(
								"peppers:select_my_address",
								"",
								Array(
									"COMPANY_ID" => $arParams['ID'],
								)
							);?>
						</div>
					</div>
				</div>
				<div class="clear"></div>
			<?endif;?>

		<?endforeach;?>

		<?if(count($arResult["GLOBAL_ERRORS"])>0):?>
			<div class="clear"></div>
			<?foreach($arResult["GLOBAL_ERRORS"] as $error):?>
				<div class="b-popup__text"><?=$error?></div>
			<?endforeach;?>
		<?endif;?>

		<?if (!$arResult["CHECK"]):?>
			<div class="clear"></div>
			<div class="globalError">
				У вас есть ошибки в заполнении
			</div>
		<?endif;?>
		<div class="clear"></div>
		<div class="btn btn_red new_company"

			 data-name="<?=$arResult["COMPANY"]["NAME"]?>">
			Сохранить</div>
		<div class="btn undo_address">Отменить</div>

		<div class="preloader company"></div>
		<div class="success hidden"><?=$arResult["SUCCESS"]?></div>

		<!--Промежуточные данные-->
		<input type="hidden" class="description-form" value='<?=$arResult["FIELDS_JSON"]?>'/>
		<input type="hidden" class="ajax_submit hidden" value='<?=$templateFolder."/ajax.php";?>'/>
		<input type="hidden" id="address_individual" value='<?=$componentPath."/address_individual.php";?>'/>

		<input type="hidden" class="address_deliver" value="<?=$arResult["COMPANY"]["ADDRESS_DELIVER_ID"]?>" />
		<div class="was_eq hidden"><?=$arResult["COMPANY"]["ADDRESS_DELIVER_ID"]?></div>
	</div>
</div>

<?
//$this->addExternalCss("https://cdn.jsdelivr.net/jquery.suggestions/16.8/css/suggestions.css");
//$this->addExternalJs("https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js");
//if(BX.browser.IsIE9==true){
//    $this->addExternalJs("https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js");
//}
//$this->addExternalJs("https://cdn.jsdelivr.net/jquery.suggestions/16.8/js/jquery.suggestions.min.js");
?>
