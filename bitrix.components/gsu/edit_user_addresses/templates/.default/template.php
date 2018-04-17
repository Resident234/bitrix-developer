<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult["FIELDS"]["PASS"]["VALUE"]);
?>

<div class="b-vacancies__item__name"><?=$arResult["NAME"]?></div>
<div class="b-vacancies__item__wrap">
	<div class="b-form">
		<?foreach($arResult["KEYS"]["INPUT"] as $key):?>
			<div class="<?=$arResult["FIELDS"][$key]["CSS_CLASS"]?>">
				<div class="b-form__row__name">
					<?if($arResult["FIELDS"][$key]["REQUIRED"]) echo "*"?>
					<?=$arResult["FIELDS"][$key]["NAME"]?>
				</div>
				<div class="b-form__row__input email_field<?=$arResult["FIELDS"][$key]["CLASS_ERROR"]?>">
					<input class="input_<?=$key?>"  type="text"
						   value="<?=$arResult["FIELDS"][$key]["VALUE"]?>" />

					<div class="b-form__row__input__error">
						<?=$arResult["FIELDS"][$key]["ERROR_MESSAGE"]?>
					</div>
				</div>
			</div>
		<?endforeach;?>
		<div class="clear"></div>
		<!--
		<div class="b-form__row">
			<div class="b-form__row__name">
				Пропуск:
			</div>
			<div class="b-form__row__input email_field ">
				<select name="input_PASS" class="input_PASS">
					<?foreach($arResult["ENUM_PROPS"][PASS_PROP_ID] as $prop):?>
						<option <?if($arResult["FIELDS"]["PASS"]["VALUE"]==$prop["ID"]) echo "selected";?> value="<?=$prop["ID"]?>"><?=$prop["VALUE"]?></option>
					<?endforeach;?>
				</select>
			</div>
		</div>
		<div class="b-form__row">
			<div class="b-form__row__name">
				Лифт:
			</div>
			<div class="b-form__row__input email_field ">
				<select name="input_ELEVATOR" class="input_ELEVATOR">
					<?foreach($arResult["ENUM_PROPS"][ELEVATOR_PROP_ID] as $prop):?>
						<option <?if($arResult["FIELDS"]["ELEVATOR"]["VALUE"]==$prop["ID"]) echo "selected";?> value="<?=$prop["ID"]?>"><?=$prop["VALUE"]?></option>
					<?endforeach;?>
				</select>
			</div>
		</div>
		-->
		<div class="clear"></div>

		<div class="clear"></div>
		<div class="btn btn_red save_address" data-id="<?=$arParams["ID"]?>">Сохранить</div>
		<a class="btn delete_address" data-id="<?=$arParams["ID"]?>" href="#">Удалить адрес</a>

		<div class="ajax_path" style="display: none"><?=$arResult["AJAX_ADDRESS"]?></div>
		<div class="delete_path" style="display: none"><?=$arResult["AJAX_DELETE"]?></div>
		<div class="preloader address"></div>
		<div class="success" style="display: none"><?=$arResult["SUCCESS"]?></div>
	</div>
</div>




