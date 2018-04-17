<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?//dump($arResult["CurrentStep"]);

if ($arResult["CurrentStep"] < 6):?>
<input type="hidden" class="companyPropId" value="<?=NAME_COMPANY_ID?>">
<input type="hidden" class="companyConsPropId" value="<?=NAME_COMPANY_CONSIGNEE_ID?>">
<input type="hidden" class="consigneePropId" value="<?=CONSIGNEE_ID?>">
<input type="hidden" class="addressPropId" value="<?if($arResult['PERSON_TYPE']==4):?><?=ADDRESS_ID?><?else:?>75<?endif;?>">


<form method="post" action="<?= htmlspecialcharsbx($arParams["PATH_TO_ORDER"]) ?>" name="order_form">
	<input type="hidden" class="deliveryInfo" value="<?=$arResult['DELIVERY_INFO']?>" name="DELIVERY_INFO">
	<input type="hidden" class="deliveryInfoFormatted" value="<?=$arResult['DELIVERY_INFO_FORMATTED']?>" name="DELIVERY_INFO_FORMATTED">
	<?=bitrix_sessid_post()?>
	<div class="b-order">
		<?endif;?>


		<?
		/* if ($arResult["CurrentStep"] == 1) //шаг выбора типа платильщика
		 {
			 //$arResult["CurrentStep"]++;
			 include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/step1.php");
		 }*/
		if ($arResult["CurrentStep"] == 2) //шаг "Оформление заказа"
		{
			$prev = "Вернуться в корзину";
			$next = "Далее. 2. Способы доставки";
			$APPLICATION->SetTitle("1. Оформление заказа");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/step2.php");
		}
		elseif ($arResult["CurrentStep"] == 3) //шаг "Способ доставки"
		{
			$prev = "Назад. 1. Оформление заказа";
			$next = "Далее. 3. Способы оплаты";
			$APPLICATION->SetTitle("2. Способ доставки");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/step3.php");
		}
		elseif ($arResult["CurrentStep"] == 4) //шаг "Способ оплаты"
		{
			$prev = "Назад. 2. Способы доставки";
			$next = "Завершить покупку";
			$APPLICATION->SetTitle("3. Способ оплаты");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/step4.php");
		}
		elseif ($arResult["CurrentStep"] == 5) //Завершение
		{
			$APPLICATION->SetTitle("Спасибо!");
			include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/step5.php");
		}
		?>
		<?if ($arResult["CurrentStep"] > 0 && $arResult["CurrentStep"] <= 7):?>
			<input type="hidden" name="ORDER_PRICE" value="<?= $arResult["ORDER_PRICE"] ?>">
			<input type="hidden" name="ORDER_WEIGHT" value="<?= $arResult["ORDER_WEIGHT"] ?>">
			<input type="hidden" name="SKIP_FIRST_STEP" value="<?= $arResult["SKIP_FIRST_STEP"] ?>">
			<input type="hidden" name="SKIP_SECOND_STEP" value="<?= $arResult["SKIP_SECOND_STEP"] ?>">
			<input type="hidden" name="SKIP_THIRD_STEP" value="<?= $arResult["SKIP_THIRD_STEP"] ?>">
			<input type="hidden" name="SKIP_FORTH_STEP" value="<?= $arResult["SKIP_FORTH_STEP"] ?>">
		<?endif?>

		<?if ($arResult["CurrentStep"] > 1 && $arResult["CurrentStep"] <= 6):?>
			<input type="hidden" name="PERSON_TYPE" value="<?= $arResult["PERSON_TYPE"] ?>">
			<input type="hidden" name="BACK" value="">
		<?endif?>

		<?if ($arResult["CurrentStep"] > 2 && $arResult["CurrentStep"] <= 6):?>
			<input type="hidden" name="PROFILE_ID" value="<?= $arResult["PROFILE_ID"] ?>">
			<input type="hidden" name="DELIVERY_LOCATION" value="<?= $arResult["DELIVERY_LOCATION"] ?>">
			<?
			$dbOrderProps = CSaleOrderProps::GetList(
				array("SORT" => "ASC"),
				array("PERSON_TYPE_ID" => $arResult["PERSON_TYPE"], "ACTIVE" => "Y", "UTIL" => "N"),
				false,
				false,
				array("ID", "TYPE", "SORT")
			);
			while ($arOrderProps = $dbOrderProps->Fetch())
			{
				if ($arOrderProps["TYPE"] == "MULTISELECT")
				{
					if (count($arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]]) > 0)
					{
						for ($i = 0; $i < count($arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]]); $i++)
						{
							?><input type="hidden" name="ORDER_PROP_<?= $arOrderProps["ID"] ?>[]" value="<?= $arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]][$i] ?>"><?
						}
					}
					else
					{
						?><input type="hidden" name="ORDER_PROP_<?= $arOrderProps["ID"] ?>[]" value=""><?
					}
				}
				else
				{
					?><input type="hidden" name="ORDER_PROP_<?= $arOrderProps["ID"] ?>" value="<?= $arResult["POST"]["ORDER_PROP_".$arOrderProps["ID"]] ?>"><?
				}
			}
			?>
		<?endif?>

		<?if ($arResult["CurrentStep"] > 3 && $arResult["CurrentStep"] < 6):?>
			<input type="hidden" name="DELIVERY_ID" value="<?= is_array($arResult["DELIVERY_ID"]) ? implode(":", $arResult["DELIVERY_ID"]) : IntVal($arResult["DELIVERY_ID"]) ?>">
		<?endif?>

		<?if ($arResult["CurrentStep"] > 4 && $arResult["CurrentStep"] < 6):?>
			<input type="hidden" name="TAX_EXEMPT" value="<?= $arResult["TAX_EXEMPT"] ?>">
			<input type="hidden" name="PAY_SYSTEM_ID" value="<?= $arResult["PAY_SYSTEM_ID"] ?>">
			<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="<?= $arResult["PAY_CURRENT_ACCOUNT"] ?>">
		<?endif?>

		<?if ($arResult["CurrentStep"] < 6):?>
			<input type="hidden" class="CurrentStep" name="CurrentStep" value="<?= ($arResult["CurrentStep"] + 1) ?>">
		<?endif?>

	</div>
	<?if($arResult["CurrentStep"] < 5):?>
	<div class="b-order__total-info">
		<div class="b-order__total-info__contacts">
			Вы можете оформить заказ<br>
			или получить консультацию<br>
			по телефону <span>+7 (499) 322-03-81</span>
		</div>
		<div class="b-order__total-info__weight-summ">
			Общий вес заказа: <span><?=$arResult["ORDER_WEIGHT"]?> кг</span><br>
			Примерный объем заказа: <span><?=$arResult["ORDER_WEIGHT"]?> м³</span><br>

			<div class="b-order__total-info__summ">
				Общая стоимость без учета доставки:
				<b>
					<span class="b-order__total-info__summ__digit"><?=$arResult["ORDER_PRICE"]?></span> <span class="b-products__item__currency">Р</span>
				</b>
			</div>
		</div>
	</div>

	<div class="b-order__buttons">
		<?if($arResult["CurrentStep"] == 2): ?>
			<a class="btn btn_big" href="<?=$arParams["PATH_TO_BASKET"]?>"><?=$prev?></a>
		<?else:?>
			<input type="submit" class="btn btn_big backButton" name="backButton" value="<?=$prev?>"/>
		<?endif;?>
		<input type="submit" class="btn btn_big btn_red" name="contButton" value="<?=$next?>">
	</div>
</form>
<?else:?>
	</form>
	<div class="b-order__success-text">
		Спасибо!<br/>
		Ваш заказ принят и будет обработан в течении 10 минут.<br />
		При возникновении вопросов Вы можете обратиться по телефону
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => "/include/phone.php",
				"EDIT_TEMPLATE" => ""
			)
		);?><br/>
		Вся важная информация о заказе отправлена на Вашу почту.
		<?if($USER->IsAuthorized()):?>
			<br />Статус заказа можно отслеживать в разделе <a href="<?=ORDERS_ADDRESS?>">Мои заказы</a>.
		<?endif;?>
		<?if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0)
		{
			?>
			<?
			if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y")
			{
				?>
				<script language="JavaScript">
					window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
				</script>
			<?= str_replace("#LINK#", $arParams["PATH_TO_PAYMENT"]."?ORDER_ID=".urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])), GetMessage("STOF_ORDER_PAY_WIN")) ?>
			<?
			}
			else
			{?>
			<br />Для оплаты заказа нажмите на кнопку «Оплатить» ниже.
				<?if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"])>0)
			{
				include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
			}
			}
			?>
			<?
		}?>
	</div>
	<div class="b-order__buttons left">
		<a class="btn btn_big btn_red" href="/catalog/">Вернуться к покупкам</a>
	</div>
<?endif;?>
<?
/*}*/
?>
