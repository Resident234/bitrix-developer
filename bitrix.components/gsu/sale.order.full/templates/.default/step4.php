<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;
global $USER;
?>
<div class="b-order__step step4">
	<div class="b-order__svod">
		Итого по заказу:
		<table>
			<tbody><tr>
				<td>Ваш заказ на сумму:</td>
				<td><?=$arResult["ORDER_PRICE_FORMATED"]?></td>
			</tr>
			<tr>
				<td>Товарных позиций в заказе:</td>
				<td><?=count($arResult["BASKET_ITEMS"])?></td>
			</tr>
			<tr>
				<td>Способ получения товара:</td>
				<td><?=$arResult["DELIVERY"]["NAME"]?></td>
			</tr>
			<tr>
				<td>Стоимость доставки:</td>
				<td><?=SaleFormatCurrency($arResult['DELIVERY']['PRICE'],$arResult['DELIVERY']['CURRENCY']);?></td>
			</tr>
			<?if(strlen($arResult['DELIVERY_INFO'])):?>
				<tr>
					<td style="width: 20%">Подробности доставки:</td>
					<td><?=$arResult['DELIVERY_INFO']?></td>
				</tr>
			<tr>
				<?endif;?>
				<td>Сумма к оплате:</td>
				<td><?=$arResult["ORDER_TOTAL_PRICE_FORMATED"]?></td>
			</tr>
			<?if($arResult["ADDRESS"] != ""):?>
				<tr>
					<td>Адрес доставки:</td>
					<td><?=$arResult["ADDRESS"]?></td>
				</tr>
			<?endif;?>
			</tbody></table>
	</div>
</div>
<div class="b-payment">
	Выберите способ оплаты:
	<div class="b-payment__list">
		<?if(count($arResult["PAY_SYSTEM"])>0):?>
			<?
			foreach($arResult["PAY_SYSTEM"] as $arPaySystem)
			{
				if($APPLICATION->get_cookie("CITY_ID")==DEFAULT_CITY_ID||$arPaySystem["ID"]!=CASH_PAY_SYSTEM_ID):?>
					<div class="b-payment__item">
						<div class="b-payment__item__sel">
							<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>" >
						</div>
						<div class="b-payment__item__info">
							<label for="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" class="b-payment__item__title">
								<?= $arPaySystem["PSA_NAME"] ?>
							</label>
							<div class="b-payment__item__descr ">
								<?
								if (strlen($arPaySystem["DESCRIPTION"])>0)
									echo $arPaySystem["DESCRIPTION"]."<br />";
								?>
							</div>
						</div>
					</div>
				<?endif;?>
				<?
			}
			?>
		<?endif;?>
	</div>
</div>
