<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

<div class="b-cabinet"> <? global $USER;
	if ($USER->IsAuthorized()):?>
		<div class="b-cabinet__title">Мои данные</div>

		<div class="b-cabinet__personal-info">
			<div class="b-cabinet__col" style="width: 40%;">
				<div class="b-cabinet__personal-info__block">
					<b> 
						<?if($arResult["USER"]["LAST_NAME"]) echo $arResult["USER"]["LAST_NAME"]?> 
						<?=$arResult["USER"]["NAME"] ?> 
						<?if($arResult["USER"]["SECOND_NAME"]) echo $arResult["USER"]["SECOND_NAME"]?>
					</b>
					<br/>

					<?if($arResult["USER"]["PERSONAL_PHONE"]):?>
						Телефон мобильный: <?= $arResult["USER"]["PERSONAL_PHONE"] ?><br/>
					<?endif;?>

					<?foreach($arResult["USER"]["UF_PHONE_NUMBERS"] as $phone){
						echo "Телефон: ".$phone."<br />";
					}?>

					E-mail: <?= $arResult["USER"]["EMAIL"] ?> </div>

					<?if($arResult["USER"]["ADDRESSES"]):?>
						<div class="b-cabinet__title">Мои адреса</div>
						<div class="b-cabinet__personal-info__block">
							<?foreach($arResult["USER"]["ADDRESSES"] as $arItem):?>
								<?=$arItem?>
								<br/>
							<?endforeach;?>
						</div>
					<?endif;?>
					<?if($arResult["USER"]["IS_OPT"]):?>
						<a href="<?=PERSONAL_ADDRESSES?>" class="btn">+ Добавить адрес</a>
					<?endif;?>
			</div>

			<?if($arResult["USER"]["IS_OPT"]):?>
				<div class="b-cabinet__col" style="width: 50%;">
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

					<?if($arResult["USER"]["COMPANIES"]):?>
						<div class="b-cabinet__personal-info__block">
							<div class="b-cabinet__title">Мои компании</div>
							<?foreach($arResult["USER"]["COMPANIES"] as $arItem):?>
								<?=$arItem?>
								<br/>
							<?endforeach;?>
						</div>
					<?endif;?>
					<a href="<?=PERSONAL_COMPANIES?>" class="btn">+ Добавить команию</a>
				</div>
			<?endif;?>
		</div>
	<? else:?>
		<div class="b-cabinet__title">Вы не авторизованы</div>
	<? endif; ?>
</div>
