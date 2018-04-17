<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($_SERVER);
?>
<div class="b-main-calc">
	<div class="b-main-calc__title">Подбери аксессуар для своего устройства!</div>
	<div class="b-main-calc__row">
		<select class="producers">
			<option>Производитель устройства</option>
			<?foreach($arResult["PRODUCERS"] as $producer):?>
				<option value="<?=$producer["ID"]?>"><?=$producer["NAME"]?></option>
			<?endforeach;?>
		</select>
	</div>
	<div class="b-main-calc__row b-main-calc__row_delimiter"></div>
	<div class="b-main-calc__row">
		<select class="models">
			<option>Модель устройства</option>
		</select>
	</div>

	<div class="b-main-calc__row b-main-calc__row_delimiter"></div>


		<div class="b-main-calc__row locked">
			<a class="b-main-calc__btn" href="#" target="_blank">Cмотреть аксессуары</a>
			<div class="b-lock"></div>
		</div>
	<!--<div class="preloader filter"></div>-->
	<input type="hidden" class="getProducersPath" value="<?=$componentPath."/ajax.php";?>" />
	<input type="hidden" class="propertyId" value="<?=AKSSESUAR_DLYA_ID?>" />
</div>