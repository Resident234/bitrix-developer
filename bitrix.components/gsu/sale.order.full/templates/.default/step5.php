<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<div class="b-order">
	<div class="b-order__step step5">
		<div class="b-order__svod">
			Итого по заказу:
			<table>
				<tbody>
				<tr>
					<td>Номер заказа:</td>
					<td><?= $arResult["ORDER_ID"] ?></td>
				</tr>
				<tr>
					<td>Ваш заказ на сумму:</td>
					<td><?= $arResult["ORDER_PRICE_FORMATED"] ?></td>
				</tr>
				<? if ($APPLICATION->get_cookie("COUNT_BASKET")): ?>
					<tr>
						<td>Товарных позиций в заказе:</td>
						<td><?= $APPLICATION->get_cookie("COUNT_BASKET") ?></td>
					</tr>
				<? endif; ?>
				<tr>
					<td>Тип доставки/получения товара:</td>
					<td><?= $arResult["DELIVERY"]["NAME"] ?></td>
				</tr>
				<tr>
					<td>Сумма к оплате:</td>
					<td><?= $arResult["ORDER_TOTAL_PRICE_FORMATED"] ?></td>
				</tr>
				<? if ($APPLICATION->get_cookie("ADDRESS")): ?>
					<tr>
						<td>Адрес доставки:</td>
						<td><?= $APPLICATION->get_cookie("ADDRESS") ?></td>
					</tr>
				<? endif; ?>
				<tr>
					<td>Способ оплаты:</td>
					<td><?= $arResult["PAY_SYSTEM"]["PSA_NAME"] ?></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>