<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>
<?if ($USER->IsAuthorized()):?>
	<div class="b-cabinet">

		<?if($arResult["USER"]["IS_OPT"]):?>
			<div class="b-cabinet__my-data">
				Моя цена: <b><?=$arResult["USER"]["PRICE_NAME"]?></b>  |  Персональная скидка: <b><?=$arResult["USER"]["UF_DISCOUNT"]?>%</b>
			</div>

			<div class="b-cabinet__personal-info">
				<div class="b-cabinet__col" style="width:250px;  margin-right: 20px;">

					<div class="b-cabinet__price-info">
							Опт10 – объем закупа от 10 000 руб./мес<br />
							Опт50 – объем закупа от 50 000 руб./мес<br />
							Опт150 – объем закупа от 150 000 руб./мес<br />
							Опт300 – побъем закупа от 300 000 руб./мес
						</div>

				</div>
				<div class="b-cabinet__col" style="width:50%">
					<?if ($arResult["MANAGER"]):?>
						<div class="b-cabinet__personal-info__block">

							<b>Персональный менеджер:</b>
							<br/>
							<?if($arResult["MANAGER"]["LAST_NAME"]) echo $arResult["MANAGER"]["LAST_NAME"]?>
							<?= $arResult["MANAGER"]["NAME"]?>
							<?if($arResult["MANAGER"]["SECOND_NAME"]) echo $arResult["MANAGER"]["SECOND_NAME"]?>
							<br/>

							<?if ($arResult["MANAGER"]["PERSONAL_PHONE"]):?>
								Телефон: <?= $arResult["MANAGER"]["PERSONAL_PHONE"] ?> |
								<a href="#call_me" class="open_popup"> Заказать звонок</a>
								<div class="b-popup" id="call_me">
									<?$APPLICATION->IncludeComponent(
										"peppers:request_call",
										"",
										Array(
											"IBLOCK_TYPE" => "feedback",
											"IBLOCK_ID" => REQUEST_CALL_IBLOCK_ID,
											"USER_NAME" => $arResult["USER"]["NAME"],
											"USER_PHONE" => $arResult["USER"]["PERSONAL_PHONE"],
											"MANAGER_FOR_ANSWER" => $arResult["MANAGER"]["ID"],
										),
										false
									);?>
								</div>
							<? endif;?>

							<br/>E-mail: <?= $arResult["MANAGER"]["EMAIL"] ?> |
							<a href="#send_letter" class="open_popup">Написать письмо</a>
							<div class="b-popup" id="send_letter">
								<?$APPLICATION->IncludeComponent(
									"peppers:send_message",
									"",
									Array(
										"IBLOCK_TYPE" => "feedback",
										"IBLOCK_ID" => MESSAGES_IBLOCK_ID,
										"USER_NAME" => $arResult["USER"]["NAME"],
										"USER_EMAIL" => $arResult["USER"]["EMAIL"],
										"MANAGER_FOR_ANSWER" => $arResult["MANAGER"]["ID"],
									)
								);?>
							</div>
						</div>
					<?endif;?>
				</div>
			</div>
		<?endif;?>

		<div class="b-form">
			<?$APPLICATION->IncludeComponent(
				"peppers:edit_user_data",
				"",
				Array()
			);?>

		</div>

	</div>
<? else:?>
	<div class="b-cabinet__title">Вы не авторизованы</div>
<? endif; ?>
